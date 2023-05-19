<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;
use Freezemage\Config\Feature\FilenameGenerator;


class PhpExporter implements ExporterInterface
{
    protected ?string $filename = null;

    public function export(ConfigInterface $config, FilenameGenerator $filenameGenerator): void
    {
        $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($config->getConfig(), true) . ';' . PHP_EOL;

        if (empty($this->filename)) {
            $this->filename = $filenameGenerator->generateFilename($content);
        }

        $file = fopen($this->filename, 'wb');
        fwrite($file, $content);
        fclose($file);
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
