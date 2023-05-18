<?php

namespace Freezemage\Config\Feature\Filename;

use Freezemage\Config\Feature\FilenameGenerator;

final class Md5Generator implements FilenameGenerator
{
    public function generateFilename(string $content): string
    {
        return md5($content);
    }
}