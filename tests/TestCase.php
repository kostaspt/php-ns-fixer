<?php

namespace PhpNsFixer\Tests;

use Symfony\Component\Finder\SplFileInfo;

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
     * @param array $parts
     * @return string
     */
    protected function joinPath(array $parts): string
    {
        return join(DIRECTORY_SEPARATOR, $parts);
    }

    /**
     * @param string $content
     * @param string $relativePath
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockFile(string $content, string $relativePath = 'Foo/Bar')
    {
        $stub = $this->createMock(SplFileInfo::class);

        $stub->method('getRelativePath')->willReturn($relativePath);
        $stub->method('getContents')->willReturn($content);

        return $stub;
    }
}
