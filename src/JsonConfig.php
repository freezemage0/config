<?php


namespace Freezemage\Config;


class JsonConfig extends FileConfig {
    protected $config;

    public function getConfig(): array {
        if (!isset($this->config)) {
            $content = file_get_contents($this->filename);
            $config = json_decode($content, true);

            if (json_last_error() != JSON_ERROR_NONE) {
                throw new MalformedConfigException(
                    json_last_error_msg(),
                    json_last_error()
                );
            }

            $this->config = $config;
        }

        return $this->config;
    }

    public function format(): string {
        return 'json';
    }
}