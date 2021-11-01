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
    
}
