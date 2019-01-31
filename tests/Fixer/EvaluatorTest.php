<?php

namespace PhpNsFixer\Tests\Fixer;

use PhpNsFixer\Fixer\Evaluator;
use PhpNsFixer\Fixer\Result;
use PhpNsFixer\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class EvaluatorTest extends TestCase
{
    /** @test */
    public function generic_valid_namespace()
    {
        $content = <<<'EOF'
<?php

namespace App\Foo\Bar;

class Baz {
    //
}
EOF;
        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file, 'App');
        $this->doTest($file, $result, true, 'App\Foo\Bar');
    }

    /** @test */
    public function inline_valid_namespace()
    {
        $content = <<<'EOF'
<?php namespace App\Foo\Bar;

class Baz {
    //
}
EOF;

        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file, 'App');
        $this->doTest($file, $result, true, 'App\Foo\Bar');
    }

    /** @test */
    public function braced_valid_namespace()
    {
        $content = <<<'EOF'
<?php

namespace App\Foo\Bar {
    class Baz {
        //
    }
}
EOF;

        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file, 'App');
        $this->doTest($file, $result, true, 'App\Foo\Bar');
    }

    /** @test */
    public function generic_valid_namespace_without_prefix()
    {
        $content = <<<'EOF'
<?php

namespace Foo\Bar;

class Baz {
    //
}
EOF;

        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file);
        $this->doTest($file, $result, true, 'Foo\Bar');
    }

    /** @test */
    public function valid_namespace_top_level_file()
    {
        $content = <<<'EOF'
<?php

namespace App;

class Baz {
    //
}
EOF;

        $file = $this->mockFile($content, '');
        $result = (new Evaluator())->check($file, 'App');
        $this->doTest($file, $result, true, 'App');
    }

    /** @test */
    public function invalid_namespace()
    {
        $content = <<<'EOF'
<?php

namespace App\Foo;

class Baz {
    //
}
EOF;

        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file, 'App');
        $this->doTest($file, $result, false, 'App\Foo\Bar');
    }

    /** @test */
    public function without_namespace()
    {
        $content = <<<'EOF'
<?php

class Baz {
    //
}
EOF;

        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file, 'App');
        $this->doTest($file, $result, false, 'App\Foo\Bar');
    }

    /** @test */
    public function without_namespace_ignoring_empty()
    {
        $content = <<<'EOF'
<?php

class Baz {
    //
}
EOF;

        $file = $this->mockFile($content);
        $result = (new Evaluator())->check($file, 'App', true);
        $this->doTest($file, $result, true, '');
    }

    /**
     * @param SplFileInfo $file
     * @param Result $result
     * @param bool $isValid
     * @param string $namespace
     */
    private function doTest(SplFileInfo $file, Result $result, bool $isValid, string $namespace)
    {
        $this->assertEquals($file->getRelativePath(), $result->getFile()->getRelativePath());

        $this->assertEquals($namespace, $result->getExpected());
        if ($isValid) {
            $this->assertEquals($namespace, $result->getActual());
        } else {
            $this->assertNotEquals($namespace, $result->getActual());
        }

        $this->assertEquals($isValid, $result->isValid());
    }
}
