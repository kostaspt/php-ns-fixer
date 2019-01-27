<?php

namespace PhpNsFixer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\SplFileInfo;

class FileProcessedEvent extends Event
{
    /**
     * @var SplFileInfo
     */
    public $file;

    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }
}
