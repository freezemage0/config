<?php


namespace Freezemage\Config\Exporter;


use Freezemage\Config\ConfigInterface;
use Freezemage\Config\Exception\UnsupportedNestingException;
use Freezemage\Config\Feature\FilenameGenerator;


class IniExporter implements ExporterInterface
{
    protected ?string $filename = null;

    public function export(ConfigInterface $config, FilenameGenerator $filenameGenerator): void
    {
        $data = $config->getConfig();

        $content = '';

        foreach ($data as $section => $items) {
            if (is_array($items)) {
                $content .= sprintf('[%s]', $section) . PHP_EOL;

                foreach ($items as $name => $item) {
                    if (is_array($item)) {
                        throw new UnsupportedNestingException(
                            'Ini files do not support nesting sections.'
                        );
                    }
                    $content .= sprintf('%s=%s', $name, $item) . PHP_EOL;
                }
            } else {
                $content .= sprintf('%s=%s', $section, $items) . PHP_EOL;
            }
        }

        if (empty($this->filename)) {
            $this->filename = $filenameGenerator->generateFilename($content) . '.ini';
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
