<?php


namespace Freezemage\Config\Importer;


interface ImporterInterface
{
    public function import(): array;

    public function setFilename(string $filename): void;

    public function getFilename(): ?string;
}