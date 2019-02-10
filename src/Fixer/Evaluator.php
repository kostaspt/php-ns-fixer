<?php

declare(strict_types=1);

namespace PhpNsFixer\Fixer;

use Spatie\Regex\Regex;
use Spatie\Regex\RegexFailed;
use Symfony\Component\Finder\SplFileInfo;

final class Evaluator
{
    /**
     * Check if the file's namespace is valid.
     *
     * @param SplFileInfo $file
     * @param string $prefix
     * @param bool $skipEmpty
     * @return Result
     */
    public function check(SplFileInfo $file, string $prefix = '', bool $skipEmpty = false): Result
    {
        $expectedNamespace = $this->guessNamespaceFromPath($file, $prefix);
        $actualNamespace = $this->parseNamespaceFromFile($file);

        if ($skipEmpty && $actualNamespace === '') {
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
        $namespace = strval(Regex::replace('/\//', '\\', $file->getRelativePath())->result());

        if (mb_strlen($prefix) !== 0) {
            $namespace = mb_strlen($namespace) !== 0 ? $prefix . '\\' . $namespace : $prefix;
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

        try {
            return trim($regex->results()[0]->group(1));
        } catch (RegexFailed $exception) {
            return '';
        }
    }
}
