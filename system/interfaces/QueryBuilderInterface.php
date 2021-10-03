<?php

namespace system\interfaces;

interface QueryBuilderInterface {
    
    const DB_OR = ' OR';
    const DB_AND = ' AND';
    const LEFT_QUOTE = ' (';
    const RIGHT_QUOTE = ')';
    const DESC = ' DESC';
    const ASC = ' ASC';
    const INNER = '';
    const LEFT = 'LEFT ';
    const RIGHT = 'RIGHT ';
    const OUTER = 'OUTER ';
    const COUNT = 'COUNT()';

    /**
     * Дает уточнения выборки данных посредством ключевого слова WHERE.
     * @param string $column Столбец.
     * @param string $sign Знак (=, >, <, >=, <= и т.д.).
     * @param string $value Значение. 
     * @return Model Тот же объект, из которого вызвана функция.    
     */
    public function where(string $column, string $sign, string $value): self;

    /**
     * Добавляет к строке запроса дополнительные данные, указываемые в аргументе <b>$query</b>.
     * @param const $add_item Константа, которая может быть равна: <ul><li>Model::AND - добавить AND</li><li>Model::OR - добавить OR</li><li>Model::LEFT_QUOTE - добавить левую круглую скобку</li><li>Model::RIGHT_QUOTE - добавить правую круглую скобку</li></ul>
     * @return Model Тот же объект, из которого вызвана функция.    
     */
    public function add(string $add_item): self;

    /**
     * Сортирует значения посредством ключевого слова ORDER BY
     * @param string $column Столбец, по которому сортируются данные
     * @param const $mode Режим сортировки: <ul><li>Model::DESC - по убыванию</li><li>Model::ASC - по возрастанию</li></ul>
     * @return Model Тот же объект, из которого вызвана функция    
     */
    public function orderBy(string $column, string $mode = ""): self;

    /**
     * Группирует значения посредством ключевого слова GROUP BY
     * @param string $columns Столбцы, подверженные группировке (если ассоциативный, ключ - таблица, значение - столбец)
     * @return Model Тот же объект, из которого вызвана функция    
     */
    public function groupBy(array $columns): self;

    /**
     * Определяет количество строк из выборки посредством ключевого слова LIMIT
     * @param int $limit Количество строк
     * @return Model Тот же объект, из которого вызвана функция    
     */
    public function limit(int $limit): self;

    /**
     * Определяет смещение посредством ключевого слова OFFSET
     * @param int $offset Смещение   
     */
    public function offset(int $offset): void;
    
    /**
     * Получение id последней вставленной записи
     * @return string id последней вставленной строки
     */
    public function getLastInsertId(): string;
    
}
