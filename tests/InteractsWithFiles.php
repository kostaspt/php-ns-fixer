<?php

namespace PhpNsFixer\Tests;

use FilesystemIterator;
use SplFileInfo;

trait InteractsWithFiles
{
    protected $testTempPath;

    protected function setUp()
    {
        parent::setUp();

        $this->testTempPath = $this->testPath . '/temp/foo';

        @mkdir($this->testTempPath, 0777, true);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->deleteTempDirectory();
    }

    /**
     * Delete the auto-generated "temp" directory.
     */
    protected function deleteTempDirectory()
    {
        $this->deleteDirectory($this->testPath . '/temp');
    }

    /**
     * Delete a (non-empty) directory.
     *
     * @param string $directory
     * @return bool
     */
    protected function deleteDirectory(string $directory)
    {
        if (! is_dir($directory)) {
            return false;
        }

        collect(new FilesystemIterator($directory))
            ->each(function(SplFileInfo $item) {
                if ($item->isDir() && ! $item->isLink()) {
                    $this->deleteDirectory($item->getPathname());
                } else {
                    @unlink($item->getPathname());
                }
            });

        return @rmdir($directory);
    }
}
