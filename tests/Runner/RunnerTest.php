<?php

namespace PhpNsFixer\Tests\Runner;

use PhpNsFixer\Event\FileProcessedEvent;
use PhpNsFixer\Fixer\Result;
use PhpNsFixer\Runner\Runner;
use PhpNsFixer\Runner\RunnerOptions;
use PhpNsFixer\Tests\InteractsWithFiles;
use PhpNsFixer\Tests\TestCase;
use Spatie\Regex\Regex;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RunnerTest extends TestCase
{
    use InteractsWithFiles;

    /** @test */
    public function normal_run()
    {
        $validContent = <<<'EOF'
<?php

namespace Foo;

class Bar {
    //
}
EOF;

        $invalidContent = <<<'EOF'
<?php

namespace App;

class Baz {
    //
}
EOF;
        $validFile = $this->createTempFile($validContent, 'Bar.php');
        $invalidFile = $this->createTempFile($invalidContent, 'Baz.php');

        $options = new RunnerOptions([$validFile, $invalidFile], 'Foo');
        $problematicFiles = (new Runner($options))->run();

        $this->assertCount(1, $problematicFiles);
        $this->assertEquals(
            $invalidFile->getRealPath(),
            $problematicFiles
                ->first()
                ->getFile()
                ->getRealPath()
        );
        $this->assertFileEquals(
            $invalidFile->getRealPath(),
            $problematicFiles
                ->first()
                ->getFile()
                ->getRealPath()
        );
        $this->doTest($problematicFiles->first(), true);
    }

    /** @test */
    public function skip_empty_run()
    {
        $validContent = <<<'EOF'
<?php

namespace Foo;

class Bar {
    //
}
EOF;

        $invalidContent = <<<'EOF'
<?php

class Baz {
    //
}
EOF;
        $validFile = $this->createTempFile($validContent, 'Bar.php');
        $invalidFile = $this->createTempFile($invalidContent, 'Baz.php');

        $options = new RunnerOptions([$validFile, $invalidFile], 'Foo', true);
        $problematicFiles = (new Runner($options))->run();

        $this->assertCount(0, $problematicFiles);
    }

    /** @test */
    public function dry_run()
    {
        $validContent = <<<'EOF'
<?php

namespace Foo;

class Bar {
    //
}
EOF;

        $invalidContent = <<<'EOF'
<?php

namespace App;

class Baz {
    //
}
EOF;
        $validFile = $this->createTempFile($validContent, 'Bar.php');
        $invalidFile = $this->createTempFile($invalidContent, 'Baz.php');

        $options = new RunnerOptions([$validFile, $invalidFile], 'Foo', false, true);
        $problematicFiles = (new Runner($options))->run();

        $this->assertCount(1, $problematicFiles);
        $this->assertEquals(
            $invalidFile->getRealPath(),
            $problematicFiles
                ->first()
                ->getFile()
                ->getRealPath()
        );
        $this->assertFileEquals(
            $invalidFile->getRealPath(),
            $problematicFiles
                ->first()
                ->getFile()
                ->getRealPath()
        );
        $this->doTest($problematicFiles->first(), false);
    }

    /** @test */
    public function fires_events()
    {
        $validContent = <<<'EOF'
<?php

namespace Foo;

class Bar {
    //
}
EOF;

        $file = $this->createTempFile($validContent, 'Bar.php');

        $firedEvent = null;
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(FileProcessedEvent::class, function (FileProcessedEvent $event) use (&$firedEvent) {
            $firedEvent = $event;
        });

        $options = new RunnerOptions([$file], 'Foo');
        (new Runner($options, $dispatcher))->run();

        $this->assertEquals($file->getRealPath(), $firedEvent->getFile()->getRealPath());
        $this->assertFileEquals($file->getRealPath(), $firedEvent->getFile()->getRealPath());
    }

    private function doTest(Result $result, bool $shouldFileBeChanged = false)
    {
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
