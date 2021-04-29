<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;


class PhpExporter implements ExporterInterface {
    protected $filename;

    public function export(ConfigInterface $config): void {
        $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($config, true) . ';' . PHP_EOL;

        if (empty($this->filename)) {
            $this->filename = md5($content) . '.php';
        }

        $file = fopen($this->filename, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    public function setFilename(string $filename): void {
        $this->filename = $filename;
    }
}