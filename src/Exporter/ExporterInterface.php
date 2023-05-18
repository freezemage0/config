<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;
use Freezemage\Config\Feature\FilenameGenerator;


interface ExporterInterface
{
    /**
     * Implementors are expected to generate filename if it is absent.
     * The filename generation result MUST always be the same for the same configuration.
     */
    public function export(ConfigInterface $config, FilenameGenerator $filenameGenerator): void;

    public function setFilename(string $filename): void;

    public function getFilename(): ?string;
}
