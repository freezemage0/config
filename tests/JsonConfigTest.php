<?php


namespace Freezemage\Config;


use Freezemage\Config\Exception\MalformedConfigException;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\Importer\JsonImporter;
use PHPUnit\Framework\TestCase;


class JsonConfigTest extends TestCase {
    public function testImport(): void {
        $importer = new JsonImporter();
        $exporter = new JsonExporter();

        $importer->setFilename(__DIR__ . '/asset/config.json');
        $config = new ImmutableConfig($importer, $exporter);

        $this->assertEquals(
            array(
                'database' => array(
                    'username' => 'user',
                    'password' => 'passwd'
                )
            ),
            $config->getConfig()
        );

    }

    public function testExport(): void {
        $importer = new JsonImporter();
        $exporter = new JsonExporter();

        $filename = __DIR__ . '/asset/test_json_file.json';
        $exporter->setFilename($filename);

        $config = new ImmutableConfig($importer, $exporter);
        $config = $config->set('username', 'user')->set('password', 'passwd');
        $config->save();

        $this->assertFileExists($filename);
        unlink($filename);
    }

    public function testCannotImport(): void {
        $importer = new JsonImporter();
        $exporter = new JsonExporter();

        $filename = __DIR__ . '/asset/invalid-config.json';

        file_put_contents($filename, '[invalid-json');

        $importer->setFilename($filename);
        $config = new ImmutableConfig($importer, $exporter);

        $this->expectException(MalformedConfigException::class);
        $config->getConfig();
    }
}