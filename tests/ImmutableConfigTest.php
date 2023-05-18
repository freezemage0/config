<?php


namespace Freezemage\Config\Test;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Exporter\IniExporter;
use Freezemage\Config\Exporter\JsonExporter;
use Freezemage\Config\Exporter\PhpExporter;
use Freezemage\Config\ImmutableConfig;
use Freezemage\Config\Importer\ImporterInterface;
use Freezemage\Config\Importer\IniImporter;
use Freezemage\Config\Importer\JsonImporter;
use Freezemage\Config\Importer\PhpImporter;
use Freezemage\Config\Settings;
use PHPUnit\Framework\TestCase;


class ImmutableConfigTest extends TestCase
{
    public function testGetWithKeyChaining(): void
    {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array(
                'database' => array(
                    'username' => 'user'
                )
            ));

        $importer->expects($this->once())->method('getFilename')->willReturn(__DIR__ . '/asset/config.json');

        $settings = $this->settings();

        $config = new ImmutableConfig($importer, $exporter, $settings);
        $value = $config->get('database.username');
        $this->assertSame('user', $value);
    }

    public function testGetSubsectionWithKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array(
                'database' => array(
                    'username' => 'user',
                    'password' => 'passwd'
                )
            ));

        $importer->expects($this->once())->method('getFilename')->willReturn(__DIR__ . '/asset/config.json');

        $config = new ImmutableConfig($importer, $exporter, $this->settings());
        $value = $config->get('database');
        $this->assertSame(['username' => 'user', 'password' => 'passwd'], $value);
    }

    public function testGetWithKeyChainingDefaultValue(): void
    {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())
            ->method('import')
            ->willReturn(['database' => []]);

        $importer->expects($this->once())->method('getFilename')->willReturn(__DIR__ . '/asset/config.json');

        $config = new ImmutableConfig($importer, $exporter, $this->settings());
        $value = $config->get('database.username', 'non-existent-value');
        $this->assertSame('non-existent-value', $value);
    }

    private function settings(): Settings
    {
        return new Settings();
    }

    public function testSetWithKeyChaining(): void
    {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $config = new ImmutableConfig($importer, $exporter, $this->settings());

        $config = $config->set('database.username', 'user')->set('database.password', 'password');
        $this->assertSame(
            array(
                'database' => array(
                    'username' => 'user',
                    'password' => 'password',
                )
            ),
            $config->getConfig()
        );
    }

    public function testSetWithKeyChainingOnAlreadyExistingKey(): void
    {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())->method('getFilename')->willReturn(__DIR__ . '/asset/config.json');
        $importer->expects($this->once())->method('import')->willReturn([
            'database' => [
                'username' => 'user',
                'password' => 'password'
            ]
        ]);

        $config = new ImmutableConfig($importer, $exporter, $this->settings());

        $config = $config->set('database.username', 'admin');
        $this->assertSame('admin', $config->get('database.username'));
    }

    public function testGetWithoutKeyChaining(): void
    {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array('database.username' => 'user'));

        $importer->expects($this->once())->method('getFilename')->willReturn(__DIR__ . '/asset/config.json');

        $config = new ImmutableConfig($importer, $exporter, $this->settings());
        $config->disableKeyChaining();

        $value = $config->get('database.username');
        $this->assertSame('user', $value);
    }

    public function testSetWithoutKeyChaining(): void
    {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $config = new ImmutableConfig($importer, $exporter, $this->settings());
        $config->disableKeyChaining();

        $config = $config->set('database.username', 'user')->set('database.password', 'password');

        $this->assertSame(
            array('database.username' => 'user', 'database.password' => 'password'),
            $config->getConfig()
        );
    }

    public function testExtractSection(): void
    {
        $importer = new JsonImporter();
        $exporter = new JsonExporter();

        $importer->setFilename(__DIR__ . '/asset/config.json');
        $exporter->setFilename(__DIR__ . '/asset/config-section.json');

        $config = new ImmutableConfig($importer, $exporter, $this->settings());
        $section = $config->extractSection('database');
        $section->save();
        $this->assertFileExists(__DIR__ . '/asset/config-section.json');
        $this->assertJsonStringEqualsJsonString(
            json_encode(['username' => 'user', 'password' => 'passwd']),
            file_get_contents(__DIR__ . '/asset/config-section.json')
        );
        unlink(__DIR__ . '/asset/config-section.json');
    }

    /**
     * @dataProvider dependenciesProvider
     */
    public function testImport(ImporterInterface $importer, ExporterInterface $exporter, string $file): void
    {
        $importer->setFilename($file);

        $config = new ImmutableConfig(
            $importer,
            $exporter,
            $this->settings()
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
    public function testExport(ImporterInterface $importer, ExporterInterface $exporter, string $file): void
    {
        $importer->setFilename($file);

        $config = new ImmutableConfig(
            $importer,
            $exporter,
            $this->settings()
        );

        $beforeExport = $config->getConfig();
        $config->save();

        $this->assertFileExists($exporter->getFilename());

        $importer->setFilename($exporter->getFilename());
        $this->assertEquals($beforeExport, $config->getConfig());
        unlink($exporter->getFilename());
    }

    public function dependenciesProvider(): array
    {
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