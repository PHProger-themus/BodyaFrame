<?php

namespace system\classes;

use phpDocumentor\Reflection\Types\Resource_;
use system\interfaces\FilesHelperInterface;

class FilesHelper implements FilesHelperInterface
{

    public static function count(string $path, string $mode = self::FILES): int
    {
        $dir = opendir($path);
        $count = 0;
        $countMethod = 'count' . ucfirst($mode);

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
        $file = file_get_contents($path);

        if ($word != self::ALL) {
            $count = preg_match_all('/(' . $word . ')\b/ui', $file);
        } else {
            $count = preg_match_all('/[\w]+\b/ui', $file);
        }

        return $count;
    }

}
