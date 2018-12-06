<?php

namespace PhpNsFixer;

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo;
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

    public static function composerConfig(string $path): ?SplFileInfo
    {
        $finder = SymfonyFinder::create()
            ->name('composer.json')
            ->in($path)
            ->depth('== 0')
            ->getIterator();

        $finder->rewind();

        return $finder->current();
    }
}
