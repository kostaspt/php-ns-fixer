<?php

namespace PhpNsFixer\Tests;

use PhpNsFixer\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FinderTest extends TestCase
{
    use InteractsWithFiles;

    /** @test */
    public function lists_target_files()
    {
        @touch($this->joinPath([$this->testTempPath, '.git']));
        @touch($this->joinPath([$this->testTempPath, '.ignoreme.php']));
        @touch($this->joinPath([$this->testTempPath, 'bar.php']));
        @touch($this->joinPath([$this->testTempPath, 'baz.phpt']));
        @touch($this->joinPath([$this->testTempPath, 'ruby.rb']));
        @touch($this->joinPath([$this->testTempPath, 'python.py']));
        @touch($this->joinPath([$this->testTempPath, 'package.json']));
        @mkdir($this->joinPath([$this->testTempPath, 'vendor']));
        @touch($this->joinPath([$this->testTempPath, 'vendor/autoload.php']));

        $files = Finder::discover($this->testTempPath);

        $this->assertEquals(2, $files->count());
        $this->assertEquals($this->joinPath([$this->testTempPath, 'bar.php']), $files->keys()->get(0));
        $this->assertEquals($this->joinPath([$this->testTempPath, 'baz.phpt']), $files->keys()->get(1));
    }
    
    /** @test */
    public function finds_composer_json()
    {
        @touch($this->joinPath([$this->testTempPath, 'composer.json']));
        @mkdir($this->joinPath([$this->testTempPath, 'other']));
        @touch($this->joinPath([$this->testTempPath, 'other/composer.json']));

        $composer = Finder::composerConfig($this->testTempPath);
        $this->assertInstanceOf(SplFileInfo::class, $composer);
        $this->assertEquals($this->joinPath([$this->testTempPath, 'composer.json']), $composer->getRealPath());
    }

    /** @test */
    public function returns_null_when_no_composer_json()
    {
        @mkdir($this->joinPath([$this->testTempPath, 'other']));
        @touch($this->joinPath([$this->testTempPath, 'other/composer.json']));

        $composer = Finder::composerConfig($this->testTempPath);

        $this->assertNull($composer);
    }
}
