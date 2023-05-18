<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;
use Freezemage\Config\Feature\FilenameGenerator;


class JsonExporter implements ExporterInterface
{
    protected ?string $filename;

    public function export(ConfigInterface $config, FilenameGenerator $filenameGenerator): void
    {
        $content = $config->getConfig();
        $content = json_encode($content);

        if (empty($this->filename)) {
            $this->filename = $filenameGenerator->generateFilename($content);
        }

        file_put_contents($this->filename, $content);
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
