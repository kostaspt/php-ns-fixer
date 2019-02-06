<?php

declare(strict_types=1);

namespace PhpNsFixer\Finder;

use Symfony\Component\Finder\Finder as SymfonyFinder;
use Tightenco\Collect\Support\Collection;

final class FileFinder
{
    public static function list(string $path): Collection
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
