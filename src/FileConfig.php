<?php


namespace Freezemage\Config;


abstract class FileConfig extends ConfigImplementation {
    protected $filename;

    public function setFilename(string $filename): void {
        $this->filename = $filename;
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     * @throws MissingConfigNameException
     */
    public function get(string $key, $defaultValue = null) {
        if (!isset($this->filename)) {
            throw new MissingConfigNameException();
        }

        return parent::get($key, $defaultValue);
    }


    abstract public function format(): string;
}