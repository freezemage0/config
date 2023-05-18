<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Importer\ImporterInterface;
use Freezemage\Config\Internal\ChainedValue;


final class ImmutableConfig implements ConfigInterface
{
    private ImporterInterface $importer;
    private ExporterInterface $exporter;
    /** @var array<array-key, ChainedValue> */
    private array $config;
    private Settings $settings;

    public function __construct(ImporterInterface $importer, ExporterInterface $exporter, Settings $settings)
    {
        $this->importer = $importer;
        $this->exporter = $exporter;
        $this->settings = $settings;
    }

    public function __clone()
    {
        if (isset($this->config)) {
            $this->config = array_map(static fn(ChainedValue $value): ChainedValue => clone $value, $this->config);
        }
    }

    /**
     * Transforms a section of config into a separate config.
     * Detached section behaves as a whole separate config, but points to original file.
     * See {@link ImporterInterface::setFilename()} and {@link ExporterInterface::setFilename()}
     * on how to change the import/export filename.
     *
     * @param string $name
     * @return ImmutableConfig
     */
    public function extractSection(string $name): self
    {
        $section = $this->get($name);

        $config = clone $this;
        $config->config = [];
        $config->parse($section);

        return $config;
    }

    /**
     * Reads parameter from configuration.
     * Can use `key chaining` in order to retrieve nested configurations.
     *
     * Example of key chaining:
     *  Configuration:
     *      array(
     *          'database' => array(
     *              'username' => 'user',
     *              'password' => 'passwd'
     *          )
     *      )
     *  Key chain 'database.username' will return 'user'.
     *
     * Key chaining makes it impossible to read configuration keys which contain dot.
     *
     * @param string $key
     * @param null $defaultValue
     *
     * @return mixed|null
     */
    public function get(string $key, $defaultValue = null)
    {
        $config = $this->load();

        if ($this->isKeyChainingEnabled()) {
            $parts = explode('.', $key);

            foreach ($parts as $index => $part) {
                $config = array_filter(
                    $config,
                    static fn(ChainedValue $value): bool => isset($value->chain[$index]) && $value->chain[$index] === $part
                );
            }

            if (empty($config)) {
                return $defaultValue;
            }

            $offset = count($parts);
            return array_reduce(
                $config,
                static function (array $accumulator, ChainedValue $value) use ($offset) {
                    $pointer = &$accumulator;

                    foreach ($value->chain as $index => $element) {
                        if ($index < $offset) {
                            continue;
                        }
                        if (!isset($pointer[$element])) {
                            $pointer[$element] = [];
                        }
                        $pointer = &$pointer[$element];
                    }
                    $pointer = $value->value;

                    return $accumulator;
                },
                []
            );
        } else {
            foreach ($config as $chainedValue) {
                if ($chainedValue->getFullName() === $key) {
                    return $chainedValue->value;
                }
            }
        }

        return $defaultValue;
    }

    private function load(): array
    {
        if (isset($this->config)) {
            return $this->config;
        }

        $this->config = [];

        $filename = $this->importer->getFilename();
        $config = !empty($filename) && is_file($filename) ? $this->importer->import() : array();

        $this->parse($config);

        return $this->config;
    }

    private function parse(array $config, array $chain = []): void
    {
        foreach ($config as $name => $value) {
            $currentChain = [...$chain, $name];
            if (is_array($value)) {
                $this->parse($value, $currentChain);
            } else {
                $this->config[] = new ChainedValue($currentChain, $value);
            }
        }
    }

    public function isKeyChainingEnabled(): bool
    {
        return $this->settings->keyChaining;
    }

    /**
     * Lazy-loads and returns full configuration.
     *
     * @return array
     */
    public function getConfig(): array
    {
        $config = $this->load();

        return array_reduce(
            $config,
            static function (array $accumulator, ChainedValue $value) {
                $pointer = &$accumulator;

                foreach ($value->chain as $element) {
                    if (!isset($pointer[$element])) {
                        $pointer[$element] = [];
                    }
                    $pointer = &$pointer[$element];
                }
                $pointer = $value->value;

                return $accumulator;
            },
            []
        );
    }

    /**
     * Clones the current instance of config and sets the configuration by key.
     * Can use key chaining in order to set nested configuration values.
     *
     * Example of key chaining:
     *  `set` called with parameters 'database.username', 'user'.
     *  Configuration will have the following values:
     *      array(
     *          'database' => array(
     *              'username' => 'user',
     *              'password' => 'passwd'
     *          )
     *      )
     *
     * Key chaining makes it impossible to read configuration keys which contain dot.
     *
     * @param $key
     * @param $value
     *
     * @return ConfigInterface
     */
    public function set(string $key, $value): ConfigInterface
    {
        $clone = clone $this;

        if ($this->isKeyChainingEnabled()) {
            $parts = explode('.', $key);

            $chains = $clone->load();
            foreach ($parts as $index => $part) {
                $chains = array_filter(
                    $chains,
                    static fn(ChainedValue $value): bool => isset($value->chain[$index]) && $value->chain[$index] === $part
                );
            }
            if (empty($chains)) {
                $clone->config[] = new ChainedValue($parts, $value);
            } else {
                $chainedValue = array_shift($chains);
                $chainedValue->value = $value;
            }
        } else {
            $clone->config[] = new ChainedValue([$key], $value);
        }

        return $clone;
    }

    /**
     * Creates new configuration.
     */
    public function save(): void
    {
        $this->exporter->export($this, $this->settings->filenameGenerator);
    }

    public function getImporter(): ImporterInterface
    {
        return $this->importer;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setImporter(ImporterInterface $importer): void
    {
        $this->importer = $importer;
    }

    public function getExporter(): ExporterInterface
    {
        return $this->exporter;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setExporter(ExporterInterface $exporter): void
    {
        $this->exporter = $exporter;
    }

    public function enableKeyChaining(): void
    {
        $this->settings->keyChaining = true;
    }

    public function disableKeyChaining(): void
    {
        $this->settings->keyChaining = false;
    }
}
