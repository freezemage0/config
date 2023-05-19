<?php


namespace Freezemage\Config\Importer;


use Freezemage\Config\Exception\MalformedConfigException;
use Freezemage\Config\Exception\MissingConfigNameException;


/**
 * @psalm-suppress MissingConstructor
 * $filename property is intentionally is in uninitialized state. Initialization happens after object construction.
 */
class IniImporter implements ImporterInterface
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
        $content = parse_ini_file($this->filename, true);

        if ($content === false) {
            throw new MalformedConfigException('Failed to parse .ini file.');
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