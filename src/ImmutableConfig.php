<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
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

    public function __construct(ImporterInterface $importer, ExporterInterface $exporter) {
        $this->importer = $importer;
        $this->exporter = $exporter;
    }

    public function get(string $key, $defaultValue = null) {
        $config = $this->getConfig();

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

    public function set($key, $value): ConfigInterface {
        $clone = clone $this;

        $parts = explode('.', $key);
        $clone->config = array_merge_recursive(
                $this->getConfig(),
                $this->buildConfig($parts, $value)
        );

        return $clone;
    }

    protected function buildConfig(array $parts, $value): array {
        $part = array_shift($parts);

        if (count($parts) > 0) {
            return array($part => $this->buildConfig($parts, $value));
        }

        return array($part => $value);
    }

    public function getConfig(): array {
        if (!isset($this->config)) {
            $this->config = $this->importer->import();
        }

        return $this->config;
    }

    public function save(): void {
        $this->exporter->export($this);
    }

    public function setImporter(ImporterInterface $importer): void {
        $this->importer = $importer;
    }

    public function setExporter(ExporterInterface $exporter): void {
        $this->exporter = $exporter;
    }

    public function getImporter(): ImporterInterface {
        return $this->importer;
    }

    public function getExporter(): ExporterInterface {
        return $this->exporter;
    }
}