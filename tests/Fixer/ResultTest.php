<?php

namespace PhpNsFixer\Tests\Fixer;

use PhpNsFixer\Fixer\Result;
use PhpNsFixer\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ResultTest extends TestCase
{
    /** @test */
    public function default_values()
    {
        $file = $this->createMock(SplFileInfo::class);
        $result = new Result($file);

        $this->assertInstanceOf(SplFileInfo::class, $result->getFile());
        $this->assertEquals('', $result->getActual());
        $this->assertEquals('', $result->getExpected());
        $this->assertEquals(false, $result->isValid());
    }

    /** @test */
    public function set_file()
    {
        $file = $this->createMock(SplFileInfo::class);
        $file->method('getContents')->willReturn('foo');
        $result = new Result($file);

        $this->assertInstanceOf(SplFileInfo::class, $result->getFile());
        $this->assertEquals('foo', $result->getFile()->getContents());
    }

    /** @test */
    public function set_valid()
    {
        $file = $this->createMock(SplFileInfo::class);
        $result = new Result($file, true);
        $this->assertEquals(true, $result->isValid());
    }

    /** @test */
    public function set_actual()
    {
        $file = $this->createMock(SplFileInfo::class);
        $result = new Result($file, false, 'foo');
        $this->assertEquals('foo', $result->getActual());
    }

    /** @test */
    public function set_expected()
    {
        $file = $this->createMock(SplFileInfo::class);
        $result = new Result($file, false, '', 'foo');
        $this->assertEquals('foo', $result->getExpected());
    }
}
