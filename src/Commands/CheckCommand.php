<?php

namespace PhpNsFixer\Commands;

use Illuminate\Support\Collection;
use PhpNsFixer\Checker;
use PhpNsFixer\Result;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CheckCommand extends Command
{
    /**
     * @var Checker
     */
    protected $checker;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->checker = new Checker();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
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

        $this->progressStart($output, $files);

        $problematicFiles = $this->collectProblematicFiles($files, $input->getOption('prefix') ?? '');

        $this->progressFinish($output);

        if ($problematicFiles->count() === 0) {
            $output->writeln("<info>No problems found! :)</info>");
            return;
        }

        $output->writeln(
            sprintf(
                "<options=bold,underscore>There %s %d wrong %s:</>\n",
                $problematicFiles->count() !== 1 ? 'are' : 'is',
                $problematicFiles->count(),
                $problematicFiles->count() !== 1 ? 'namespaces' : 'namespace'
            )
        );

        $problematicFiles
            ->each(function (Result $result, $key) use ($output) {
                $output->writeln(sprintf("%d) %s:", $key + 1, $result->file()->getRelativePathname()));
                $output->writeln(sprintf("\t<fg=red>- %s</>", $result->expected()));
                $output->writeln(sprintf("\t<fg=green>+ %s</>", $result->actual()));
            });
    }

    protected function collectProblematicFiles(Finder $files, $prefix = ''): Collection
    {
        return collect($files->getIterator())
            ->map(function (SplFileInfo $file) use ($prefix) {
                $this->progressBar->setMessage($file->getRelativePathname(), 'filename');
                $this->progressBar->advance();

                $result = $this->checker->check($file, $prefix);

                if ($result->isValid()) {
                    return null;
                }

                return $result;
            })
            ->filter()
            ->values();
    }

    /**
     * @param OutputInterface $output
     * @param Finder $files
     * @return void
     */
    protected function progressStart(OutputInterface $output, Finder $files): void
    {
        $this->progressBar = new ProgressBar($output, $files->count());

        $this->progressBar->setFormatDefinition('custom', 'Checking files... %current%/%max% (%filename%)');
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
