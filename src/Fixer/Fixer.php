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

        $actualNamespacePattern = '/namespace ' . preg_quote($result->getActual()) . '/';

        if (Regex::matchAll($actualNamespacePattern, $result->getFile()->getContents())->hasMatch()) {
            $pattern = $actualNamespacePattern;
            $replacement = 'namespace ' . $result->getExpected();
        } else {
            $pattern = '/<\?php/';
            $replacement = "<?php\n\nnamespace " . $result->getExpected() . ';';
        }

        $content = Regex::replace($pattern, $replacement, $result->getFile()->getContents())->result();

        $result
            ->getFile()
            ->openFile('w')
            ->fwrite(strval($content));

        return true;
    }
}
