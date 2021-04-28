<?php


namespace Freezemage\Config;


use Freezemage\Config\Exception\InvalidConfigFileException;
use Freezemage\Config\Exception\UnsupportedFileExtensionException;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\Importer\JsonImporter;
use PHPUnit\Framework\TestCase;


class ConfigFactoryTest extends TestCase {
    public function testCreate(): void {
        $factory = new ConfigFactory();
        $jsonConfig = $factory->create(__DIR__ . '/asset/config.json');

        $this->assertInstanceOf(JsonImporter::class, $jsonConfig->getImporter());
        $this->assertInstanceOf(JsonExporter::class, $jsonConfig->getExporter());
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