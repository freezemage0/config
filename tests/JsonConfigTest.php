<?php


namespace Freezemage\Config;


use PHPUnit\Framework\TestCase;


class JsonConfigTest extends TestCase {
    /**
     * @covers \Freezemage\Config\JsonConfig::get
     * @throws MissingConfigNameException
     */
    public function testGet(): void {
        $config = new JsonConfig();
        $config->setFilename(__DIR__ . '/asset/config.json');

        $this->assertArrayHasKey(
            'format',
            $config->get('config')
        );

        $this->assertEquals(array('json', 'ini'), $config->get('config.format'));
    }
}