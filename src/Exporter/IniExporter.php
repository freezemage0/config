<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;


class IniExporter implements ExporterInterface {
    protected $filename;

    public function export(ConfigInterface $config): void {
        $data = $config->getConfig();

        $content = '';

        foreach ($data as $section => $items) {
            if (is_array($items)) {
                $content .= sprintf('[%s]', $section) . PHP_EOL;

                foreach ($items as $name => $item) {
                    $content .= sprintf('%s=%s', $name, $item) . PHP_EOL;
                }
            } else {
                $content .= sprintf('%s=%s', $section, $items) . PHP_EOL;
            }
        }

        if (empty($this->filename)) {
            $this->filename = md5($content) . '.ini';
        }

        $file = fopen($this->filename, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    public function setFilename(string $filename): void {
        $this->filename = $filename;
    }
}