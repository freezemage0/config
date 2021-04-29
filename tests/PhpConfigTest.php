<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\PhpExporter;
use Freezemage\Config\Importer\PhpImporter;
use PHPUnit\Framework\TestCase;


class PhpConfigTest extends TestCase {
    public function testImport(): void {
        $importer = new PhpImporter();
        $exporter = new PhpExporter();

        $importer->setFilename(__DIR__ . '/asset/config.php');

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
        $importer = new PhpImporter();
        $exporter = new PhpExporter();

        $filename = __DIR__ . '/asset/test_php_config.php';
        $exporter->setFilename($filename);

        $config = new ImmutableConfig($importer, $exporter);
        $config->set('database', array('username' => 'user', 'password' => 'passwd'))->save();

        $this->assertFileExists($filename);
        unlink($filename);
    }
}