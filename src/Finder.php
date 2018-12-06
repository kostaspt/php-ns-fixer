<?php

namespace PhpNsFixer;

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Tightenco\Collect\Support\Collection;

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
