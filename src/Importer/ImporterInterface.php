<?php


namespace Freezemage\Config\Importer;


interface ImporterInterface
{
    /**
     * @return array<array-key, mixed>
     */
    public function import(): array;

    public function setFilename(string $filename): void;

    public function getFilename(): ?string;
}