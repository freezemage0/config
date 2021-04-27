<?php


namespace Freezemage\Config;


use SplObjectStorage;


class FileConfigFactory {
    /** @var FileConfig[] $configs */
    protected $configs;

    public function __construct() {
        $this->configs = new SplObjectStorage();
        $this->register(new JsonConfig());
        $this->register(new IniConfig());
    }

    public function register(FileConfig $config): void {
        if (!$this->configs->contains($config)) {
            $this->configs->attach($config);
        }
    }

    public function create(string $filename): FileConfig {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if (empty($extension)) {
            throw new InvalidConfigFileException('Unable to determine file extension.');
        }

        foreach ($this->configs as $config) {
            if ($config->format() != $extension) {
                continue;
            }

            $clone = clone $config;
            $clone->setFilename($filename);
            return $clone;
        }

        throw new UnsupportedFileExtensionException(sprintf('Unsupported file extension: %s', $extension));
    }
}