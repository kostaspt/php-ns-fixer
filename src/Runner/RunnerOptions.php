<?php

declare(strict_types=1);

namespace PhpNsFixer\Runner;

use Tightenco\Collect\Support\Collection;

final class RunnerOptions
{
    /**
     * @var Collection
     */
    private $files;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var bool
     */
    private $skipEmpty;

    /**
     * @var bool
     */
    private $dryRun;

    public function __construct(iterable $files, string $prefix = '', bool $skipEmpty = false, bool $dryRun = false)
    {
        $this->files = collect($files);
        $this->prefix = $prefix;
        $this->skipEmpty = $skipEmpty;
        $this->dryRun = $dryRun;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return bool
     */
    public function isSkipEmpty(): bool
    {
        return $this->skipEmpty;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }
}
