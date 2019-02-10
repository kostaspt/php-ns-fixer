<?php

declare(strict_types=1);

namespace PhpNsFixer\Finder;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class ConfigFinder
{
    public static function get(string $path): ?SplFileInfo
    {
        $finder = Finder::create()
            ->name('composer.json')
            ->in($path)
            ->depth('== 0')
            ->getIterator();

        $finder->rewind();

        return $finder->current();
    }
}
