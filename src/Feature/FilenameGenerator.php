<?php

namespace Freezemage\Config\Feature;

interface FilenameGenerator
{
    /**
     * Generated name **MUST** remain the same for the same value of `$content`.
     *
     * @param string $content
     * @return string
     */
    public function generateFilename(string $content): string;
}