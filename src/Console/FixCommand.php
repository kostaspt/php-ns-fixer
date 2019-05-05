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
use Tightenco\Collect\Support\Collection;

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
        $problematicFiles = $this->runFixer($this->resolveOptions($input, $files));
        $this->progressFinish($output);

        $this->printResults($output, $problematicFiles, $this->getDryRunOption($input));

        return $problematicFiles->count();
    }

    /**
     * @param RunnerOptions $options
     * @return Collection
     */
    private function runFixer(RunnerOptions $options): Collection
    {
        return (new Runner($options, $this->dispatcher))->run();
    }

    /**
     * @param InputInterface $input
     * @param Collection $files
     * @return RunnerOptions
     */
    private function resolveOptions(InputInterface $input, Collection $files)
    {
        return new RunnerOptions(
            $files,
            $this->getPrefixOption($input),
            $this->getSkipEmptyOption($input),
            $this->getDryRunOption($input)
        );
    }

    /**
     * Parse the prefix input option.
     *
     * @param InputInterface $input
     * @return string
     */
    private function getPrefixOption(InputInterface $input): string
    {
        return strval($input->getOption('prefix')) ?? '';
    }

    /**
     * Parse the skip-empty input option.
     *
     * @param InputInterface $input
     * @return bool
     */
    private function getSkipEmptyOption(InputInterface $input): bool
    {
        return boolval($input->getOption('skip-empty')) ?? false;
    }

    /**
     * Parse the dry-run input option.
     *
     * @param InputInterface $input
     * @return bool
     */
    private function getDryRunOption(InputInterface $input): bool
    {
        return boolval($input->getOption('dry-run')) ?? false;
    }
}
