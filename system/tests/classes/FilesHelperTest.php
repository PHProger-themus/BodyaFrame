<?php

namespace system\tests\classes;

use PHPUnit\Framework\TestCase;
use system\classes\FilesHelper;
use system\core\SystemException;

/**
 * @covers FilesHelper
 */
class FilesHelperTest extends TestCase
{

    private string $directory;

    public function setUp(): void
    {
        $this->directory = dirname(__DIR__) . '/dir';
    }

    public function testCountFiles()
    {
        $this->assertEquals(4, FilesHelper::count($this->directory, FilesHelper::ALL));
        $this->assertEquals(3, FilesHelper::count($this->directory));
        $this->assertEquals(1, FilesHelper::count($this->directory, FilesHelper::DIRECTORIES));
    }

    public function testDirectoryDoesNotExist()
    {
        $this->expectException(SystemException::class);
        FilesHelper::count('/where_is_this/directory');
    }

    public function testCountOfWords()
    {
        $file = $this->directory . '/testFile3.txt';
        $this->assertEquals(19, FilesHelper::countWordsInFile($file));
        $this->assertEquals(2, FilesHelper::countWordsInFile($file, 'is'));
    }

}