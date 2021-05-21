<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Feature\KeyChaining;
use Freezemage\Config\Importer\ImporterInterface;


class ImmutableConfig implements ConfigInterface {
    /**
     * @var ImporterInterface $importer
     */
    protected $importer;
    /**
     * @var ExporterInterface $exporter
     */
    protected $exporter;
    /**
     * @var array $config
     */
    protected $config;
    /**
     * @var bool $keyChaining;
     */
    protected $keyChaining;

    public function __construct(ImporterInterface $importer, ExporterInterface $exporter, KeyChaining $keyChaining) {
        $this->importer = $importer;
        $this->exporter = $exporter;
        $this->config = array();
        $this->keyChaining = $keyChaining;
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
    public function get(string $key, $defaultValue = null) {
        $config = $this->getConfig();

        if ($this->isKeyChainingEnabled()) {
            $parts = explode('.', $key);

            do {
                $part = array_shift($parts);

                if (!array_key_exists($part, $config)) {
                    return $defaultValue;
                }

                $config = $config[$part];
            } while (!empty($parts));

            return $config;
        }

        return $config[$key] ?? $defaultValue;
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
    public function extractSection(string $name): ImmutableConfig {
        $section = $this->get($name);
        
        $config = clone $this;
        $config->config = array($name => $section);
        
        return $config;
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
    public function set($key, $value): ConfigInterface {
        $clone = clone $this;

        if ($this->isKeyChainingEnabled()) {
            $parts = explode('.', $key);
            $clone->config = array_merge_recursive(
                $this->getConfig(),
                $this->buildConfig($parts, $value)
            );
        } else {
            $clone->config[$key] = $value;
        }

        return $clone;
    }

    protected function buildConfig(array $parts, $value): array {
        $part = array_shift($parts);

        if (count($parts) > 0) {
            return array($part => $this->buildConfig($parts, $value));
        }

        return array($part => $value);
    }

    /**
     * Lazy-loads and returns full configuration.
     *
     * @return array
     */
    public function getConfig(): array {
        if (!empty($this->config)) {
            return $this->config;
        }

        $filename = $this->importer->getFilename();
        $this->config = !empty($filename) && is_file($filename) ? $this->importer->import() : array();

        return $this->config;
    }

    /**
     * Creates new configuration.
     */
    public function save(): void {
        $this->exporter->export($this);
    }

    /**
     * @param ImporterInterface $importer
     *
     * @codeCoverageIgnore
     */
    public function setImporter(ImporterInterface $importer): void {
        $this->importer = $importer;
    }

    /**
     * @param ExporterInterface $exporter
     *
     * @codeCoverageIgnore
     */
    public function setExporter(ExporterInterface $exporter): void {
        $this->exporter = $exporter;
    }

    public function getImporter(): ImporterInterface {
        return $this->importer;
    }

    public function getExporter(): ExporterInterface {
        return $this->exporter;
    }

    public function enableKeyChaining(): void {
        $this->keyChaining->enable();
    }

    public function disableKeyChaining(): void {
        $this->keyChaining->disable();
    }

    public function isKeyChainingEnabled(): bool {
        return $this->keyChaining->isEnabled();
    }
}