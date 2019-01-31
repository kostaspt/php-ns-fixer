<?php

namespace PhpNsFixer\Tests\Fixer;

use PhpNsFixer\Fixer\Evaluator;
use PhpNsFixer\Fixer\Fixer;
use PhpNsFixer\Fixer\Result;
use PhpNsFixer\Tests\InteractsWithFiles;
use PhpNsFixer\Tests\TestCase;
use Spatie\Regex\Regex;

class FixerTest extends TestCase
{
    use InteractsWithFiles;

    /** @test */
    public function valid_namespace()
    {
        $content = <<<'EOF'
<?php

namespace Foo;

class Bar {
    //
}
EOF;
        $file = $this->createTempFile($content);
        $result = (new Evaluator())->check($file, 'Foo');
        $fileChanged = (new Fixer())->fix($result);
        $this->doTest($result, $fileChanged, false);
    }

    /** @test */
    public function invalid_namespace()
    {
        $content = <<<'EOF'
<?php

namespace App\Foo;

class Bar {
    //
}
EOF;

        $file = $this->createTempFile($content);
        $result = (new Evaluator())->check($file, 'Foo');
        $fileChanged = (new Fixer())->fix($result);
        $this->doTest($result, $fileChanged, true);
    }

    /** @test */
    public function without_namespace()
    {
        $content = <<<'EOF'
<?php

class Bar {
    //
}
EOF;

        $file = $this->createTempFile($content);
        $result = (new Evaluator())->check($file, 'Foo');
        $fileChanged = (new Fixer())->fix($result);
        $this->doTest($result, $fileChanged, true);
    }

    /** @test */
    public function without_namespace_ignoring_empty()
    {
        $content = <<<'EOF'
<?php

class Bar {
    //
}
EOF;

        $file = $this->createTempFile($content);
        $result = (new Evaluator())->check($file, 'Foo', true);
        $fileChanged = (new Fixer())->fix($result);
        $this->doTest($result, $fileChanged, false);
    }

    private function doTest(Result $result, bool $fileChanged, bool $shouldFileBeChanged = false)
    {
        $this->assertEquals($fileChanged, $shouldFileBeChanged);

        if ($shouldFileBeChanged && $result->getActual() !== '') {
            $this->assertFalse(
                Regex::matchAll(
                    '/namespace ' . preg_quote($result->getActual()) . '/',
                    $result->getFile()->getContents()
                )->hasMatch()
            );
        }
        if ($shouldFileBeChanged) {
            $this->assertTrue(
                Regex::matchAll(
                    '/namespace ' . preg_quote($result->getExpected()) . '/',
                    $result->getFile()->getContents()
                )->hasMatch()
            );
        }
    }
}
