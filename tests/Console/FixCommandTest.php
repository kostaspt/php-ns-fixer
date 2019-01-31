<?php

namespace PhpNsFixer\Tests\Console;

use PhpNsFixer\Console\FixCommand;
use PhpNsFixer\Tests\InteractsWithFiles;
use PhpNsFixer\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class FixCommandTest extends TestCase
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

    /** @test */
    public function dry_check()
    {
        $content = <<<'EOF'
<?php

namespace Baz;

class Bar {
    //
}
EOF;

        file_put_contents($this->testPath . '/temp/foo/Bar.php', $content);

        $this->doTest(['--dry-run' => true]);
    }

    protected function doTest(array $options = [])
    {
        $application = new Application();
        $application->add(new FixCommand());

        $command = $application->find('fix');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array_merge([
            'command' => $command->getName(),
            'path'    => "{$this->testPath}/temp/foo",
            '--prefix'  => 'Foo'
        ], $options));

        $output = $commandTester->getDisplay(true);
        $this->assertMatchesSnapshot($output);
    }
}
