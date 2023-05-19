<?php


namespace Freezemage\Config\Importer;


use Freezemage\Config\Exception\MalformedConfigException;
use Freezemage\Config\Exception\MissingConfigNameException;


class JsonImporter implements ImporterInterface
{
    protected ?string $filename = null;

    /**
     * @throws MalformedConfigException
     * @throws MissingConfigNameException
     */
    public function import(): array
    {
        if (!isset($this->filename) || !is_file($this->filename)) {
            throw new MissingConfigNameException();
        }
        $content = file_get_contents($this->filename);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new MalformedConfigException(
                json_last_error_msg(),
                json_last_error()
            );
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return [$data];
        }

        return $data;
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