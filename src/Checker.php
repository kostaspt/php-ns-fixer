<?php

namespace PhpNsFixer;

use Spatie\Regex\Regex;
use Symfony\Component\Finder\SplFileInfo;

class Checker
{
    /**
     * Check if the file's namespace is valid.
     *
     * @param SplFileInfo $file
     * @param string $prefix
     * @return Result
     */
    public function check(SplFileInfo $file, string $prefix = ''): Result
    {
        $expectedNamespace = $this->namespaceFromPath($file, $prefix);
        $actualNamespace = $this->namespaceFromFile($file);

        return new Result($file, $actualNamespace === $expectedNamespace, $actualNamespace, $expectedNamespace);
    }

    /**
     * Generate the namespace based on file's path.
     *
     * @param SplFileInfo $file
     * @param string $prefix
     * @return string
     */
    private function namespaceFromPath(SplFileInfo $file, string $prefix = ''): string
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
    private function namespaceFromFile(SplFileInfo $file): string
    {
        $regex = Regex::matchAll('/namespace (.*?)(;|{|$)/', $file->getContents());

        if (! $regex->hasMatch()) {
            return '';
        }

        return trim($regex->results()[0]->group(1));
    }
}
