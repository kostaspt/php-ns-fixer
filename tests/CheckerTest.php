<?php

namespace PhpNsFixer\Tests;

use PhpNsFixer\Checker;
use PhpNsFixer\Result;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class CheckerTest extends TestCase
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
        $result = (new Checker())->check($file, 'App');
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
        $result = (new Checker())->check($file, 'App');
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
        $result = (new Checker())->check($file, 'App');
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
        $result = (new Checker())->check($file);
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
        $result = (new Checker())->check($file, 'App');
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
        $result = (new Checker())->check($file, 'App');
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
        $result = (new Checker())->check($file, 'App');
        $this->doTest($file, $result, false, 'App\Foo\Bar');
    }

    /**
     * @param string $content
     * @param string $relativePath
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockFile(string $content, string $relativePath = 'Foo\Bar')
    {
        $stub = $this->createMock(SplFileInfo::class);

        $stub->method('getRelativePath')->willReturn($relativePath);
        $stub->method('getContents')->willReturn($content);

        return $stub;
    }

    /**
     * @param SplFileInfo $file
     * @param Result $result
     * @param bool $isValid
     * @param string $namespace
     */
    private function doTest(SplFileInfo $file, Result $result, bool $isValid, string $namespace)
    {
        $this->assertEquals($file->getRelativePath(), $result->file()->getRelativePath());

        $this->assertEquals($namespace, $result->expected());
        if ($isValid) {
            $this->assertEquals($namespace, $result->actual());
        } else {
            $this->assertNotEquals($namespace, $result->actual());
        }

        $this->assertEquals($isValid, $result->isValid());
    }
}
