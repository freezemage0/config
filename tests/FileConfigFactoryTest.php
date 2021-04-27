<?php


namespace Freezemage\Config;


use PHPUnit\Framework\TestCase;


class FileConfigFactoryTest extends TestCase {
    /**
     * @covers \Freezemage\Config\FileConfigFactory::create
     *
     * @throws InvalidConfigFileException
     * @throws UnsupportedFileExtensionException
     */
    public function testCreate(): void {
        $factory = new FileConfigFactory();

        $config = $factory->create(__DIR__ . '/asset/config.json');
        $this->assertInstanceOf(JsonConfig::class, $config);

        $config = $factory->create(__DIR__ . '/asset/config.ini');
        $this->assertInstanceOf(IniConfig::class, $config);
    }

    /**
     * @covers \Freezemage\Config\FileConfigFactory::create
     *
     * @throws InvalidConfigFileException
     * @throws UnsupportedFileExtensionException
     */
    public function testMissingFileExtension(): void {
        $factory = new FileConfigFactory();

        $this->expectException(InvalidConfigFileException::class);
        $this->expectExceptionMessage('Unable to determine file extension.');

        $factory->create('config');
    }
}