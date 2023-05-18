<?php


namespace Freezemage\Config\Importer;


use Freezemage\Config\Exception\MalformedConfigException;


class PhpImporter implements ImporterInterface
{
    protected ?string $filename;

    /**
     * @throws MalformedConfigException
     */
    public function import(): array
    {
        /** @noinspection PhpIncludeInspection */
        $content = include $this->filename;
        if (empty($content) || !is_array($content)) {
            throw new MalformedConfigException('Unable to parse config file.');
        }

        return $content;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}