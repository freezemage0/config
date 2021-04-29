<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\IniExporter;
use Freezemage\Config\Importer\IniImporter;
use PHPUnit\Framework\TestCase;


class IniConfigTest extends TestCase {
    public function testImport(): void {
        $importer = new IniImporter();
        $exporter = new IniExporter();

        $importer->setFilename(__DIR__ . '/asset/config.ini');
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
        $importer = new IniImporter();
        $exporter = new IniExporter();

        $filename = __DIR__ . '/asset/test_ini_file.ini';
        $exporter->setFilename($filename);

        $config = new ImmutableConfig($importer, $exporter);
        $config = $config->set('username', 'user')->set('password', 'passwd');
        $config->save();

        $this->assertFileExists($filename);
        unlink($filename);
    }
}