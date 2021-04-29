<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;


interface ExporterInterface {
    /**
     * Implementors are expected to generate filename if it is absent.
     * The filename generation result MUST always be the same for the same configuration.
     *
     * @param ConfigInterface $config
     */
    public function export(ConfigInterface $config): void;

    public function setFilename(string $filename): void;

    public function getFilename(): ?string;
}