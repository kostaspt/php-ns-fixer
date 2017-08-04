<?php

namespace NamespaceChecker\Commands;

use NamespaceChecker\Checker;
use NamespaceChecker\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CheckCommand extends Command
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $problematicFiles;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->problematicFiles = collect();

        $this->setName('check')
            ->setDefinition([
                new InputArgument('path', InputArgument::REQUIRED, 'The path.'),
                new InputOption('prefix', 'P', InputOption::VALUE_REQUIRED, 'Namespace prefix.'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->listFiles($input->getArgument('path'));

        $progressBar = $this->initializeProgressBar($output, $files);
        $progressBar->start();

        $checker = new Checker();
        foreach ($files as $file) {
            $progressBar->setMessage($file->getRelativePathname(), 'filename');
            $progressBar->advance();

            $result = $checker->check($file, $input->getOption('prefix') ?? '');

            if (! $result->isValid()) {
                $this->problematicFiles->push($result);
            }
        }

        $progressBar->setMessage('Done', 'filename');
        $progressBar->finish();
        $output->writeln("\n");

        if ($this->problematicFiles->count() === 0) {
            $output->writeln("<info>No problems found! :)</info>");
            return;
        }

        $output->writeln(
            sprintf(
                "<options=bold,underscore>There %s %d wrong %s:</>\n",
                $this->problematicFiles->count() !== 1 ? 'are' : 'is',
                $this->problematicFiles->count(),
                $this->problematicFiles->count() !== 1 ? 'namespaces' : 'namespace'
            )
        );
        $this->problematicFiles
            ->each(function (Result $result, $key) use ($output) {
                $output->writeln(sprintf("%d) %s:", $key + 1, $result->file()->getRelativePathname()));
                $output->writeln(sprintf("\t<fg=red>- %s</>", $result->expected()));
                $output->writeln(sprintf("\t<fg=green>+ %s</>", $result->actual()));
            });
    }

    /**
     * @param OutputInterface $output
     * @param $files
     * @return ProgressBar
     */
    protected function initializeProgressBar(OutputInterface $output, Finder $files): ProgressBar
    {
        $progressBar = new ProgressBar($output, $files->count());

        $progressBar->setFormatDefinition('custom', 'Checking files... %current%/%max% (%filename%)');
        $progressBar->setFormat('custom');

        return $progressBar;
    }

    /**
     * @param string $path
     * @return Finder
     */
    protected function listFiles(string $path): Finder
    {
        return Finder::create()
            ->name('*.php')
            ->name('*.phpt')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->exclude('vendor')
            ->in($path);
    }
}
