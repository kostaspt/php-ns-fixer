<?php

namespace PhpNsFixer;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder
{
    public static function discover(string $path): Collection
    {
        return collect(
            SymfonyFinder::create()
                ->name('*.php')
                ->name('*.phpt')
                ->ignoreDotFiles(true)
                ->ignoreVCS(true)
                ->exclude('vendor')
                ->in($path)
        );
    }
}
