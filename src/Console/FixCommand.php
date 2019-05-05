<?php

declare(strict_types=1);

namespace PhpNsFixer\Console;

use PhpNsFixer\Finder\FileFinder;
use PhpNsFixer\Runner\Runner;
use PhpNsFixer\Runner\RunnerOptions;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class FixCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('fix')->setDescription('Fixes a directory');
        $this->setDefinition([
            new InputArgument('path', InputArgument::REQUIRED, 'The path.'),
            new InputOption('prefix', 'P', InputOption::VALUE_REQUIRED, 'Namespace prefix.'),
            new InputOption('skip-empty', 'E', InputOption::VALUE_NONE, 'Skip files without namespace.'),
            new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Only show which files would have been modified.')
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
            ($dryRun = boolval($input->getOption('dry-run')) ?? false)
        );
        $problematicFiles = (new Runner($runnerOptions, $this->dispatcher))->run();

        $this->progressFinish($output);

        $this->printResults($output, $problematicFiles, $dryRun);

        return $problematicFiles->count();
    }
}
