<?php


namespace Freezemage\Config\Importer;


use Freezemage\Config\Exception\MalformedConfigException;


class JsonImporter implements ImporterInterface
{
    protected ?string $filename;

    /**
     * @throws MalformedConfigException
     */
    public function import(): array
    {
        $content = file_get_contents($this->filename);
        $config = json_decode($content, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new MalformedConfigException(
                json_last_error_msg(),
                json_last_error()
            );
        }

        return $config;
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