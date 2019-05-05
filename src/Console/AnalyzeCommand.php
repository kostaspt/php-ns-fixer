<?php

namespace PhpNsFixer\Console;

use Symfony\Component\Console\Input\ArrayInput;
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
        $arguments = [
            'command'   => 'fix',
            'path'      => $input->getArgument('path'),
            '--dry-run' => true,
        ];

        if ($input->hasOption('prefix')) {
            $arguments['--prefix'] = $input->getOption('prefix');
        }

        if ($input->hasOption('skip-empty')) {
            $arguments['--skip-empty'] = '';
        }

        return $this->getApplication()->find('fix')->run(new ArrayInput($arguments), $output);
    }
}
