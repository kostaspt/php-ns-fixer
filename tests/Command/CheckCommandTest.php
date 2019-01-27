<?php

namespace PhpNsFixer\Tests\Command;

use PhpNsFixer\Tests\InteractsWithFiles;
use PhpNsFixer\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CheckCommandTest extends TestCase
{
    use MatchesSnapshots, InteractsWithFiles;

    /** @test */
    public function valid_check()
    {
        $content = <<<'EOF'
<?php

namespace Foo;

class Bar {
    //
}
EOF;

        file_put_contents($this->testPath . '/temp/foo/Bar.php', $content);

        $this->doTest();
    }

    /** @test */
    public function invalid_check()
    {
        $content = <<<'EOF'
<?php

namespace Baz;

class Bar {
    //
}
EOF;

        file_put_contents($this->testPath . '/temp/foo/Bar.php', $content);

        $this->doTest();
    }

    protected function doTest()
    {
        exec("{$this->basePath}/bin/php-ns-fixer check {$this->testPath}/temp/foo -P Foo 2> /dev/null", $output);

        $this->assertMatchesSnapshot($output);
    }
}
