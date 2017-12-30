<?php

namespace PhpNsFixer;

use Spatie\Regex\Regex;
use Symfony\Component\Finder\SplFileInfo;

class Checker
{
    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * @param SplFileInfo $file
     */
    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    /**
     * Check if the file's namespace is valid.
     *
     * @param string $prefix
     * @param bool $ignoreEmpty
     * @return Result
     */
    public function check(string $prefix = '', bool $ignoreEmpty = false): Result
    {
        $expectedNamespace = $this->namespaceFromPath($prefix);
        $actualNamespace = $this->namespaceFromFile();

        if ($ignoreEmpty && $actualNamespace === '') {
            return new Result($this->file, true, '', '');
        }

        return new Result($this->file, $actualNamespace === $expectedNamespace, $actualNamespace, $expectedNamespace);
    }

    /**
     * Generate the namespace based on file's path.
     *
     * @param string $prefix
     * @return string
     */
    private function namespaceFromPath(string $prefix = ''): string
    {
        $namespace = Regex::replace('/\//', '\\', $this->file->getRelativePath())->result();

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
     * @return string
     */
    private function namespaceFromFile(): string
    {
        $regex = Regex::matchAll('/namespace (.*?)(;|{|$)/', $this->file->getContents());

        if (! $regex->hasMatch()) {
            return '';
        }

        return trim($regex->results()[0]->group(1));
    }
}
