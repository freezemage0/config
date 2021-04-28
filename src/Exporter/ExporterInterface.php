<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;


interface ExporterInterface {
    public function export(ConfigInterface $config): void;

    public function setFilename(string $filename): void;
}