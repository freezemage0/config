<?php


namespace Freezemage\Config;


interface ConfigInterface {
    public function get(string $key, $defaultValue = null);

    public function getConfig(): array;
}