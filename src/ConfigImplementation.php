<?php


namespace Freezemage\Config;


abstract class ConfigImplementation implements ConfigInterface {
    public function get(string $key, $defaultValue = null) {
        $config = $this->getConfig();

        $parts = explode('.', $key);

        do {
            $part = array_shift($parts);

            if (!array_key_exists($part, $config)) {
                return $defaultValue;
            }

            $config = $config[$part];
        } while (!empty($parts));

        return $config;
    }
}