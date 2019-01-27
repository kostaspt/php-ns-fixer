<?php

namespace PhpNsFixer;

use PhpNsFixer\Event\FileProcessedEvent;
use Spatie\Regex\Regex;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\SplFileInfo;
use Tightenco\Collect\Support\Collection;

class Evaluator
{
    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EventDispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Evaluate a list of files.
     *
     * @param Collection $files
     * @param string $prefix
     * @param bool $ignoreEmpty
     * @return Collection
     */
    public function check(Collection $files, string $prefix = '', bool $ignoreEmpty = false)
    {
        return $files
            ->map(function (SplFileInfo $file) use ($prefix, $ignoreEmpty) {
                $result = $this->evaluate($file, $prefix, $ignoreEmpty);

                $this->fireFileProcessedEvent($file);

                if ($result->isValid()) {
                    return null;
                }

                return $result;
            })
            ->filter()
            ->values();
    }

    /**
     * Check if the file's namespace is valid.
     *
     * @param SplFileInfo $file
     * @param string $prefix
     * @param bool $ignoreEmpty
     * @return Result
     */
    public function evaluate(SplFileInfo $file, string $prefix = '', bool $ignoreEmpty = false): Result
    {
        $expectedNamespace = $this->guessNamespaceFromPath($file, $prefix);
        $actualNamespace = $this->parseNamespaceFromFile($file);

        if ($ignoreEmpty && $actualNamespace === '') {
            return new Result($file, true, '', '');
        }

        return new Result($file, $actualNamespace === $expectedNamespace, $actualNamespace, $expectedNamespace);
    }

    /**
     * Generate the namespace based on file's path.
     *
     * @param SplFileInfo $file
     * @param string $prefix
     * @return string
     */
    private function guessNamespaceFromPath(SplFileInfo $file, string $prefix = ''): string
    {
        $namespace = Regex::replace('/\//', '\\', $file->getRelativePath())->result();

        if (mb_strlen($prefix) !== 0) {
            if (mb_strlen($namespace) !== 0) {
                $namespace = $prefix . '\\' . $namespace;
            } else {
                $namespace = $prefix;
            }
        }

        return $namespace;
    }

    /**
     * Extract the namespace from a file.
     *
     * @param SplFileInfo $file
     * @return string
     */
    private function parseNamespaceFromFile(SplFileInfo $file): string
    {
        $regex = Regex::matchAll('/namespace (.*?)(;|{|$)/', $file->getContents());

        if (!$regex->hasMatch()) {
            return '';
        }

        return trim($regex->results()[0]->group(1));
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
