<?php

namespace Freezemage\Config;

use Freezemage\Config\Feature\Filename\Md5Generator;
use Freezemage\Config\Feature\FilenameGenerator;

final class Settings
{
    public bool $keyChaining;
    public FilenameGenerator $filenameGenerator;

    public function __construct(bool $keyChaining = true, FilenameGenerator $filenameGenerator = null)
    {
        $this->keyChaining = $keyChaining;
        $this->filenameGenerator = $filenameGenerator ?? new Md5Generator();
    }
}