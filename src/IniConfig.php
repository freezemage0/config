<?php


namespace Freezemage\Config;


class IniConfig extends FileConfig {
    protected $config;

    public function getConfig(): array {
        if (!isset($this->config)) {
            $config = parse_ini_file($this->filename, true);
            if ($config == false) {
                throw new MalformedConfigException(
                    'Failed to parse config file',
                    0
                );
            }

            $this->config = $config;
        }

        return $this->config;
    }

    public function format(): string {
        return 'ini';
    }
}