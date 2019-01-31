<?php

namespace PhpNsFixer\Fixer;

use Spatie\Regex\Regex;

class Fixer
{
    public function fix(Result $result): bool
    {
        if ($result->isValid()) {
            return false;
        }

        $regexActualNamespace = '/namespace ' . preg_quote($result->getActual()) . '/';

        if (Regex::matchAll($regexActualNamespace, $result->getFile()->getContents())->hasMatch()) {
            $content = Regex::replace(
                $regexActualNamespace,
                'namespace ' . $result->getExpected(),
                $result->getFile()->getContents()
            )->result();
        } else {
            $content = Regex::replace(
                '/<\?php/',
                "<?php\n\nnamespace " . $result->getExpected() . ';',
                $result->getFile()->getContents()
            )->result();
        }

        $result
            ->getFile()
            ->openFile('w')
            ->fwrite($content);

        return true;
    }
}
