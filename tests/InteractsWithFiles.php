<?php

namespace PhpNsFixer\Tests;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
     * @param string $content
     * @param string $filename
     * @return SplFileInfo
     */
    protected function createTempFile(string $content, string $filename = 'Bar.php'): SplFileInfo
    {
        $filePath = $this->testPath . '/temp/foo/' . $filename;
        file_put_contents($filePath, $content);
        return new SplFileInfo($filePath, '', $filename);
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
        if (!is_dir($directory)) {
            return false;
        }

        $existingFiles = Finder::create()->ignoreDotFiles(false)->in($directory);
        collect($existingFiles)->each(function (SplFileInfo $item) {
            if ($item->isDir() && !$item->isLink()) {
                $this->deleteDirectory($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        });

        return @rmdir($directory);
    }
}
