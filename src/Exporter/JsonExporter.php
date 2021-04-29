<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;


class JsonExporter implements ExporterInterface {
    /**
     * @var string
     */
    protected $filename;

    public function export(ConfigInterface $config): void {
        $content = $config->getConfig();
        $content = json_encode($content);

        if (empty($this->filename)) {
            $this->filename = md5($content) . '.json';
        }

        file_put_contents($this->filename, $content);
    }

    public function setFilename(string $filename): void {
        $this->filename = $filename;
    }

    public function getFilename(): ?string {
        return $this->filename;
    }
}