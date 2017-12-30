<?php

namespace PhpNsFixer\Tests;

use FilesystemIterator;
use SplFileInfo;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $testPath;

    /**
     * Constructs a test case with the given name.
     *
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->testPath = dirname(__FILE__);
        $this->basePath = dirname($this->testPath);
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

        @rmdir($directory);
    }
}
