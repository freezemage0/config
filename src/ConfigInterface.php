<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Importer\ImporterInterface;


interface ConfigInterface
{
    /**
     * @psalm-suppress MixedReturnStatement caused by $defaultValue missing a typehint, but it is intentional by design.
     * @psalm-suppress MissingReturnType typehint is not set intentionally due to mixed $defaultValue.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get(string $key, $defaultValue = null);

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return ConfigInterface
     */
    public function set(string $key, $value): self;

    public function getConfig(): array;

    public function save(): void;

    public function setImporter(ImporterInterface $importer): void;

    public function setExporter(ExporterInterface $exporter): void;

    public function getImporter(): ImporterInterface;

    public function getExporter(): ExporterInterface;
}
