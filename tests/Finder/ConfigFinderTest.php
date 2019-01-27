<?php

namespace PhpNsFixer\Tests\Finder;

use PhpNsFixer\Finder\ConfigFinder;
use PhpNsFixer\Tests\InteractsWithFiles;
use PhpNsFixer\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ConfigFinderTest extends TestCase
{
    use InteractsWithFiles;

    /** @test */
    public function gets_composer_json()
    {
        @touch($this->joinPath([$this->testTempPath, 'composer.json']));
        @mkdir($this->joinPath([$this->testTempPath, 'other']));
        @touch($this->joinPath([$this->testTempPath, 'other/composer.json']));

        $composer = ConfigFinder::get($this->testTempPath);
        $this->assertInstanceOf(SplFileInfo::class, $composer);
        $this->assertEquals($this->joinPath([$this->testTempPath, 'composer.json']), $composer->getRealPath());
    }

    /** @test */
    public function gets_null_when_no_composer_json()
    {
        @mkdir($this->joinPath([$this->testTempPath, 'other']));
        @touch($this->joinPath([$this->testTempPath, 'other/composer.json']));

        $composer = ConfigFinder::get($this->testTempPath);

        $this->assertNull($composer);
    }
}
