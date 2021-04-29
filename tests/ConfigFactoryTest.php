<?php


namespace Freezemage\Config;


use Freezemage\Config\Exception\InvalidConfigFileException;
use Freezemage\Config\Exception\UnsupportedFileExtensionException;
use Freezemage\Config\Exporter\IniExporter;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\Exporter\PhpExporter;
use Freezemage\Config\Importer\IniImporter;
use Freezemage\Config\Importer\JsonImporter;
use Freezemage\Config\Importer\PhpImporter;
use PHPUnit\Framework\TestCase;


class ConfigFactoryTest extends TestCase {
    public function configNameProvider(): array {
        return array(
            array(
                __DIR__ . '/asset/config.json',
                JsonImporter::class,
                JsonExporter::class
            ),
            array(
                __DIR__ . '/asset/config.ini',
                IniImporter::class,
                IniExporter::class
            ),
            array(
                __DIR__ . '/asset/config.php',
                PhpImporter::class,
                PhpExporter::class
            )
        );
    }

    /**
     * @dataProvider configNameProvider
     *
     * @param string $path
     * @param string $importerClass
     * @param string $exporterClass
     *
     * @throws InvalidConfigFileException
     * @throws UnsupportedFileExtensionException
     */
    public function testCreate(string $path, string $importerClass, string $exporterClass): void {
        $factory = new ConfigFactory();

        $config = $factory->create($path);
        $this->assertInstanceOf($importerClass, $config->getImporter());
        $this->assertInstanceOf($exporterClass, $config->getExporter());
    }

    public function testUnsupportedExtension(): void {
        $factory = new ConfigFactory();

        $this->expectException(UnsupportedFileExtensionException::class);
        $factory->create('unknown.extension');
    }

    public function testMissingFileExtension(): void {
        $factory = new ConfigFactory();

        $this->expectException(InvalidConfigFileException::class);
        $factory->create('no-extension');
    }
}