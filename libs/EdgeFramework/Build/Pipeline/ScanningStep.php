<?php

namespace EdgeFramework\Build\Pipeline;
use EdgeFramework\Build\Scanner\Scanner;
use EdgeFramework\Build\Scanner\Symbol;

/**
 * @extends parent<string, string[]>
 */
final class ScanningStep implements Step
{
    public function process($source)
    {
        $scanner = new Scanner();
        return $scanner->scan($source);
    }
}
