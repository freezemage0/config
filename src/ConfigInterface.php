<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Importer\ImporterInterface;


interface ConfigInterface {
    public function get(string $key, $defaultValue = null);

    public function set($key, $value): ConfigInterface;

    public function getConfig(): array;

    public function save(): void;

    public function setImporter(ImporterInterface $importer): void;

    public function setExporter(ExporterInterface $exporter): void;

    public function getImporter(): ImporterInterface;

    public function getExporter(): ExporterInterface;
}