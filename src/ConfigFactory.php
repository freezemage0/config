<?php


namespace Freezemage\Config;


use Freezemage\Config\Exception\InvalidConfigFileException;
use Freezemage\Config\Exception\UnsupportedFileExtensionException;
use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Exporter\IniExporter;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\Exporter\PhpExporter;
use Freezemage\Config\Importer\ImporterInterface;
use Freezemage\Config\Importer\IniImporter;
use Freezemage\Config\Importer\JsonImporter;
use Freezemage\Config\Importer\PhpImporter;


final class ConfigFactory
{
    protected array $importerMap;
    protected array $exporterMap;

    public function __construct()
    {
        $this->importerMap = array();
        $this->exporterMap = array();

        $this->registerImporter('json', new JsonImporter());
        $this->registerImporter('ini', new IniImporter());
        $this->registerImporter('php', new PhpImporter());

        $this->registerExporter('json', new JsonExporter());
        $this->registerExporter('ini', new IniExporter());
        $this->registerExporter('php', new PhpExporter());
    }

    public function registerImporter(string $format, ImporterInterface $importer)
    {
        $this->importerMap[$format] = $importer;
    }

    public function registerExporter(string $format, ExporterInterface $exporter)
    {
        $this->exporterMap[$format] = $exporter;
    }

    /**
     * @param string $filename
     *
     * @return ConfigInterface
     *
     * @throws InvalidConfigFileException
     * @throws UnsupportedFileExtensionException
     */
    public function create(string $filename, Settings $settings = null): ConfigInterface
    {
        $settings ??= new Settings();

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (empty($extension)) {
            throw new InvalidConfigFileException('Unable to determine file extension.');
        }

        $importer = $this->findImporter($extension);
        $importer->setFilename($filename);

        $exporter = $this->findExporter($extension);
        $exporter->setFilename($filename);

        return new ImmutableConfig($importer, $exporter, $settings);
    }

    /**
     * @throws UnsupportedFileExtensionException
     */
    public function findImporter(string $format): ImporterInterface
    {
        foreach ($this->importerMap as $f => $importer) {
            if ($f == $format) {
                return clone $importer;
            }
        }

        throw new UnsupportedFileExtensionException(sprintf('File extension "%s" is not supported.', $format));
    }

    /**
     * @throws UnsupportedFileExtensionException
     */
    public function findExporter(string $format): ExporterInterface
    {
        foreach ($this->exporterMap as $f => $exporter) {
            if ($f == $format) {
                return clone $exporter;
            }
        }

        throw new UnsupportedFileExtensionException(sprintf('File extension "%s" is not supported.', $format));
    }
}