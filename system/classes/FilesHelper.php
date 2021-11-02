<?php

namespace system\classes;

use phpDocumentor\Reflection\Types\Resource_;
use system\core\SystemException;
use system\interfaces\FilesHelperInterface;
use function PHPUnit\Framework\directoryExists;

class FilesHelper implements FilesHelperInterface
{

    public static function count(string $path, string $mode = self::FILES): int
    {
        if (file_exists($path)) {
            $dir = opendir($path);
            $count = 0;
            $countMethod = 'count' . $mode;

            while ($file = readdir($dir)) {
                if ($file != '.' && $file != '..') {
                    $filename = $path . DIRECTORY_SEPARATOR . $file;
                    if ($mode == self::ALL) {
                        $count++;
                    } else {
                        self::$countMethod($count, $filename);
                    }
                }
            }
            return $count;
        } else {
            throw new SystemException("Заданной директории \"$path\" не существует");
        }
    }

    private static function countFiles(int &$count, string $filename)
    {
        if (!is_dir($filename)) {
            $count++;
        }
    }

    private static function countDirectories(int &$count, string $filename)
    {
        if (is_dir($filename)) {
            $count++;
        }
    }

    public static function countWordsInFile(string $path, string $word = self::ALL): int
    {
        $word = mb_strtolower($word);
        $file = mb_strtolower(file_get_contents($path));

        if ($word != self::ALL) {
            $count = preg_match_all("/ $word$| $word |^$word/ui", $file);
        } else {
            $count = preg_match_all('/[\w]+\b/ui', $file);
        }

        return $count;
    }

}
