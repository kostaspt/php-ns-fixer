<?php

namespace PhpNsFixer\Console;

use PhpNsFixer\Finder\FileFinder;
use PhpNsFixer\Runner\Runner;
use PhpNsFixer\Runner\RunnerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class AnalyzeCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('analyze')->setDescription('Analyzes a directory');
        $this->setDefinition([
            new InputArgument('path', InputArgument::REQUIRED, 'The path.'),
            new InputOption('prefix', 'P', InputOption::VALUE_REQUIRED, 'Namespace prefix.'),
            new InputOption('skip-empty', 'E', InputOption::VALUE_NONE, 'Skip files without namespace.'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = FileFinder::list(strval($input->getArgument('path')));

        $this->progressStart($output, $files);

        $runnerOptions = new RunnerOptions(
            $files,
            strval($input->getOption('prefix')) ?? '',
            boolval($input->getOption('skip-empty')) ?? false,
            true
        );
        $problematicFiles = (new Runner($runnerOptions, $this->dispatcher))->run();

        $this->progressFinish($output);

        $this->printResults($output, $problematicFiles);

        return $problematicFiles->count();
    }
}
