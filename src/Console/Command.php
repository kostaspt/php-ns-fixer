<?php

namespace PhpNsFixer\Console;

use PhpNsFixer\Event\FileProcessedEvent;
use PhpNsFixer\Fixer\Result;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Tightenco\Collect\Support\Collection;

abstract class Command extends SymfonyCommand
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addListener(FileProcessedEvent::class, function (FileProcessedEvent $event) {
            $this->progressBar->setMessage($event->getFile()->getRelativePathname(), 'filename');
            $this->progressBar->advance();
        });
    }

    /**
     * @param OutputInterface $output
     * @param Collection $files
     * @return void
     */
    protected function progressStart(OutputInterface $output, Collection $files): void
    {
        ProgressBar::setFormatDefinition('custom', 'Checking files... %current%/%max% (%filename%)');

        $this->progressBar = new ProgressBar($output, $files->count());
        $this->progressBar->setFormat('custom');
        $this->progressBar->start();
    }

    /**
     * @param OutputInterface $output
     */
    protected function progressFinish(OutputInterface $output): void
    {
        $this->progressBar->setMessage('Done', 'filename');
        $this->progressBar->finish();

        $output->writeln("\n");
    }

    /**
     * @param OutputInterface $output
     * @param Collection $problematicFiles
     * @param bool $dryRun
     */
    protected function printResults(OutputInterface $output, Collection $problematicFiles, bool $dryRun = true): void
    {
        if ($problematicFiles->count() === 0) {
            $output->writeln("<info>No problems found! :)</info>");
            return;
        }

        $output->writeln(
            sprintf(
                "<options=bold,underscore>There %s %d wrong %s:</>\n",
                $this->verbForMessage($problematicFiles, $dryRun),
                $problematicFiles->count(),
                $problematicFiles->count() !== 1 ? 'namespaces' : 'namespace'
            )
        );

        $problematicFiles->each(function (Result $result, $key) use ($output) {
            $output->writeln(sprintf("%d) %s:", $key + 1, $result->getFile()->getRelativePathname()));
            $output->writeln(sprintf("\t<fg=red>- %s</>", $result->getExpected()));
            $output->writeln(sprintf("\t<fg=green>+ %s</>", $result->getActual()));
        });
    }

    /**
     * @param Collection $problematicFiles
     * @param bool $isDryRun
     * @return string
     */
    protected function verbForMessage(Collection $problematicFiles, bool $isDryRun = false): string
    {
        return $problematicFiles->count() !== 1 ? ($isDryRun ? 'are' : 'were') : ($isDryRun ? 'is' : 'was');
    }
}
