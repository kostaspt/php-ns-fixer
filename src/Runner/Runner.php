<?php

namespace PhpNsFixer\Runner;

use PhpNsFixer\Event\FileProcessedEvent;
use PhpNsFixer\Fixer\Evaluator;
use PhpNsFixer\Fixer\Fixer;
use PhpNsFixer\Fixer\Result;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;
use Tightenco\Collect\Support\Collection;

class Runner
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Evaluator
     */
    protected $evaluator;

    /**
     * @var Fixer
     */
    protected $fixer;
    /**
     * @var RunnerOptions
     */
    protected $options;

    /**
     * @param RunnerOptions $options
     * @param EventDispatcher $dispatcher
     */
    public function __construct(RunnerOptions $options, EventDispatcher $dispatcher = null)
    {
        $this->options = $options;
        $this->dispatcher = $dispatcher;
        $this->evaluator = new Evaluator();
        $this->fixer = new Fixer();
    }

    /**
     * @return Collection
     */
    public function run(): Collection
    {
        $files = $this->options
            ->getFiles()
            ->map(function (SplFileInfo $file) {
                $this->fireFileProcessedEvent($file);
                return $this->evaluator->check($file, $this->options->getPrefix(), $this->options->isSkipEmpty());
            })
            ->reject(function (Result $result) {
                return $result->isValid();
            })
            ->values();

        if (!$this->options->isDryRun()) {
            $files->each(function ($file) {
                $this->fixer->fix($file);
            });
        }

        return $files;
    }

    /**
     * Dispatch event, if dispatcher is available.
     *
     * @param SplFileInfo $file
     */
    private function fireFileProcessedEvent(SplFileInfo $file)
    {
        if ($this->dispatcher === null) {
            return;
        }

        $this->dispatcher->dispatch(FileProcessedEvent::class, new FileProcessedEvent($file));
    }
}
