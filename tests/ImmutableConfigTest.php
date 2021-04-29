<?php


namespace Freezemage\Config;


use Freezemage\Config\Exporter\ExporterInterface;
use Freezemage\Config\Importer\ImporterInterface;
use PHPUnit\Framework\TestCase;


class ImmutableConfigTest extends TestCase {
    public function testGetWithKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())
                ->method('import')
                ->willReturn(array(
                    'database' => array(
                        'username' => 'user')
                ));

        $importer->expects($this->once())->method('getFilename')->willReturn('test-filename');

        $config = new ImmutableConfig($importer, $exporter);
        $value = $config->get('database.username');
        $this->assertSame('user', $value);
    }

    public function testGetWithKeyChainingDefaultValue(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array('database' => array()));

        $importer->expects($this->once())->method('getFilename')->willReturn('test-filename');

        $config = new ImmutableConfig($importer, $exporter);
        $value = $config->get('database.username', 'non-existent-value');
        $this->assertSame('non-existent-value', $value);
    }

    public function testSetWithKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $config = new ImmutableConfig($importer, $exporter);

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

        $importer->expects($this->once())
            ->method('import')
            ->willReturn(array('database.username' => 'user'));

        $importer->expects($this->once())->method('getFilename')->willReturn('test-filename');

        $config = new ImmutableConfig($importer, $exporter);
        $config->disableKeyChaining();

        $value = $config->get('database.username');
        $this->assertSame('user', $value);
    }

    public function testSetWithoutKeyChaining(): void {
        $importer = $this->createMock(ImporterInterface::class);
        $exporter = $this->createMock(ExporterInterface::class);

        $config = new ImmutableConfig($importer, $exporter);
        $config->disableKeyChaining();

        $config = $config->set('database.username', 'user')->set('database.password', 'password');

        $this->assertSame(
            array('database.username' => 'user', 'database.password' => 'password'),
            $config->getConfig()
        );

    }
}