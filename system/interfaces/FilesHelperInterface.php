<?php

namespace system\interfaces;

interface FilesHelperInterface {
    
    /**
     * @var string Константа, говорящая функции count() о том, что нужно подсчитать директории.
    */
    public const DIRECTORIES = 'directories';
    
    /**
     * @var string Константа, говорящая функции count() о том, что нужно подсчитать файлы.
    */
    public const FILES = 'files';
    
    /**
     * @var string Константа, говорящая функции count() о том, что нужно подсчитать и файлы, и директории. <br> Она также говорит функции countWordsInFile(), что нужно посчитать все слова в файле.
    */
    public const ALL = 'all';
    
    /**
     * Считает файлы из директории.
     * @param string $path Путь до директории с файлами.
     * @param string $mode Режим подсчета (константа).
     * @return int Количество требуемого контента.   
    */
    public static function count(string $path, string $mode = self::FILES) : int;
    
    /**
     * Считает слова в файле.
     * @param string $path Путь до файла.
     * @param string $word Слово: <ul><li>FilesHelper::ALL - количество всех слов в файле</li><li>Любое другое слово - поиск именно этого слова</li></ul>
     * @return int Количество требуемых слов.   
    */
    public static function countWordsInFile(string $path, string $word = self::ALL) : int;
    
}
