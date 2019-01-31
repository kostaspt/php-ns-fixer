<?php

namespace PhpNsFixer\Tests\Runner;

use PhpNsFixer\Runner\RunnerOptions;
use PHPUnit\Framework\TestCase;

class RunnerOptionsTest extends TestCase
{
    /** @test */
    public function default_values()
    {
        $options = new RunnerOptions([]);

        $this->assertEquals(collect(), $options->getFiles());
        $this->assertEquals('', $options->getPrefix());
        $this->assertEquals(false, $options->isSkipEmpty());
        $this->assertEquals(false, $options->isDryRun());
    }

    /** @test */
    public function set_files()
    {
        $options = new RunnerOptions(['foo.txt']);

        $this->assertEquals('foo.txt', $options->getFiles()->first());
    }

    /** @test */
    public function set_prefix()
    {
        $options = new RunnerOptions([], 'foo');

        $this->assertEquals('foo', $options->getPrefix());
    }

    /** @test */
    public function set_ignore_empty()
    {
        $options = new RunnerOptions([], '', true);

        $this->assertTrue($options->isSkipEmpty());
    }

    /** @test */
    public function set_dry_run()
    {
        $options = new RunnerOptions([], '', false, true);

        $this->assertTrue($options->isDryRun());
    }
}
