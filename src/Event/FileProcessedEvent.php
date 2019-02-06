<?php

declare(strict_types=1);

namespace PhpNsFixer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\SplFileInfo;

final class FileProcessedEvent extends Event
{
    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile(): SplFileInfo
    {
        return $this->file;
    }
}
