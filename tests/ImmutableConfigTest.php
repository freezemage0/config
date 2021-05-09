<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Exporter\IniExporter;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\Exporter\PhpExporter;
use Freezemage\Config\Feature\KeyChaining;
use Freezemage\Config\Importer\ImporterInterface;
use Freezemage\Config\Importer\IniImporter;
use Freezemage\Config\Importer\JsonImporter;
use Freezemage\Config\Importer\PhpImporter;
use PHPUnit\Framework\TestCase;


class ImmutableConfigTest extends TestCase {
    public function testGetWithKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $keyChaining = new KeyChaining();
        
        $importer->expects($this->once())
                ->method('import')
                ->willReturn(array(
                    'database' => array(
                        'username' => 'user')
                ));

        $importer->expects($this->once())->method('getFilename')->willReturn('test-filename');

        $config = new ImmutableConfig($importer, $exporter, $keyChaining);
        $value = $config->get('database.username');
        $this->assertSame('user', $value);
    }

    public function testGetWithKeyChainingDefaultValue(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $keyChaining = new KeyCHaining();
        
        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array('database' => array()));

        $importer->expects($this->once())->method('getFilename')->willReturn('test-filename');

        $config = new ImmutableConfig($importer, $exporter, $keyChaining);
        $value = $config->get('database.username', 'non-existent-value');
        $this->assertSame('non-existent-value', $value);
    }

    public function testSetWithKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $keyChaining = new KeyChaining();
        
        $config = new ImmutableConfig($importer, $exporter, $keyChaining);

        $config = $config->set('database.username', 'user')->set('database.password', 'password');
        $this->assertSame(
            array('database' => array(
                'username' => 'user',
                'password' => 'password',
            )),
            $config->getConfig()
        );
    }

    public function testGetWithoutKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $keyChaining = new KeyChaining();
        
        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array('database.username' => 'user'));

        $importer->expects($this->once())->method('getFilename')->willReturn('test-filename');

        $config = new ImmutableConfig($importer, $exporter, $keyChaining);
        $config->disableKeyChaining();

        $value = $config->get('database.username');
        $this->assertSame('user', $value);
    }

    public function testSetWithoutKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);
        $keyChaining = new KeyChaining();
        
        $config = new ImmutableConfig($importer, $exporter, $keyChaining);
        $config->disableKeyChaining();

        $config = $config->set('database.username', 'user')->set('database.password', 'password');

        $this->assertSame(
            array('database.username' => 'user', 'database.password' => 'password'),
            $config->getConfig()
        );

    }
    
    public function testExtractSection(): void {
        $importer = new JsonImporter();
        $exporter = new JsonExporter();
        
        $importer->setFilename(__DIR__ . '/asset/config.json');
        $exporter->setFilename(__DIR__ . '/asset/config-section.json');
        
        $keyChaining = new KeyChaining();
        
        $config = new ImmutableConfig($importer, $exporter, $keyChaining);
        $section = $config->extractSection('database');
        $section->save();
        $this->assertFileExists(__DIR__ . '/asset/config-section.json');
        unlink(__DIR__ . '/asset/config-section.json');
    }
    
    /**
     * @dataProvider dependenciesProvider
     */
    public function testImport(ImporterInterface $importer, ExporterInterface $exporter, string $file): void {
        $importer->setFilename($file);
        
        $keyChaining = new KeyChaining();
        
        $config = new ImmutableConfig(
                $importer,
                $exporter,
                $keyChaining
        );
        
        $content = $config->getConfig();
        $this->assertEquals(array('database' => array('username' => 'user', 'password' => 'passwd')), $content);
    }
    
    /**
     * @dataProvider dependenciesProvider
     *
     * @param ImporterInterface $importer
     * @param ExporterInterface $exporter
     * @param string $file
     */
    public function testExport(ImporterInterface $importer, ExporterInterface $exporter, string $file): void {
        $keyChaining = new KeyChaining();
        
        $importer->setFilename($file);
        
        $config = new ImmutableConfig(
                $importer,
                $exporter,
                $keyChaining
        );
        
        $beforeExport = $config->getConfig();
        $config->save();
        
        $this->assertFileExists($exporter->getFilename());
        
        $importer->setFilename($exporter->getFilename());
        $this->assertEquals($beforeExport, $config->getConfig());
        unlink($exporter->getFilename());
    }
    
    public function dependenciesProvider(): array {
        return array(
                array(
                        new JsonImporter(),
                        new JSonExporter(),
                        __DIR__ . '/asset/config.json'
                ),
                array(
                        new PhpImporter(),
                        new PhpExporter(),
                        __DIR__ . '/asset/config.php'
                ),
                array(
                        new IniImporter(),
                        new IniExporter(),
                        __DIR__ . '/asset/config.ini'
                )
        );
    }
}