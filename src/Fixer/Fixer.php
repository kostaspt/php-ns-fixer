<?php

declare(strict_types=1);

namespace PhpNsFixer\Fixer;

use Spatie\Regex\Regex;

final class Fixer
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
            ->fwrite(strval($content));

        return true;
    }
}
