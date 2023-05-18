<?php


namespace Freezemage\Config\Importer;


use Freezemage\Config\Exception\MalformedConfigException;


class IniImporter implements ImporterInterface
{
    protected ?string $filename;

    public function import(): array
    {
        $content = parse_ini_file($this->filename, true);

        if ($content === false) {
            throw new MalformedConfigException('Failed to parse .ini file.');
        }

        return $content;
    }

    public function getFilename(): ?string
    {
        return $this->filename ?? null;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}