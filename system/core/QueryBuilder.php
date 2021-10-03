<?php

namespace system\core;

use app\attributes\ToDatabase;
use PDO;
use ReflectionClass;
use system\classes\ArrayHolder;
use system\classes\SafetyManager;
use system\interfaces\QueryBuilderInterface;

abstract class QueryBuilder extends Model implements QueryBuilderInterface
{

    private string $query = '';
    private ArrayHolder $dbi;
    private PDO $db;

    public function __construct(string|bool $database = 'db', ArrayHolder $data = null)
    {
        if ($database !== false) {
            if (Cfg::$get->db['active']) {
                $this->setDatabase($database);
            }
            parent::__construct($data);
        }
    }

    protected function setDatabase(string $database = null): void
    {
        if (Cfg::$get->db['useAttributes']) {
            $methods = (new ReflectionClass(new static(false)))->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $attributes = $method->getAttributes(ToDatabase::class);
                if ($attributes) {
                    $attribute = $attributes[0]->newInstance();
                    $database = $attribute->db;
                    break;
                }
            }
        }
        $this->dbi = ArrayHolder::new(Cfg::$get->db['databases'][$database]);
        $this->connect();
    }

    /**
     * Выполняет подключение к серверу базы данных.
     */
    private function connect(): void
    {
        $dsn = 'mysql:host=' . $this->dbi->host . ';dbname=' . $this->dbi->database . ';charset=utf8';
        $this->db = new PDO($dsn, $this->dbi->username, $this->dbi->password);
    }

    private function debugOrNothing(string $query)
    {
        if (Cfg::$get->db_debug) {
            db_debug($query);
            echo $this->db->errorInfo()[2];
        }
    }

    public function execute(string $query = null)
    {
        if (is_null($query)) {
            $query = $this->query;
        }
        $sth = $this->db->prepare($query);
        $sth->execute();
        $this->debugOrNothing($query);
        return $sth;
    }

    public function getRowsAsArray(string $query = null)
    {
        $sth = $this->execute($query);
        $rows = $sth->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$data) {
            foreach ($data as &$value) {
                $value = SafetyManager::filterString($value);
            }
        }
        return $rows;
    }

    public function getRows(string $query = null)
    {
        $rows = $this->getRowsAsArray($query);
        foreach ($rows as &$data) {
            $data = ArrayHolder::new($data);
        }
        return $rows;
    }

    private function addTablePrefix($column) : string // Добавляем префикс, если столбец записан в виде table.column
    {
        if (str_contains($column, '.')) {
            return $this->dbi->prefix . $column;
        }
        return $column;
    }

    protected function columnsToString(array $columns)
    {
        $columns_str = null;
        if ($columns != []) {
            foreach ($columns as $column => $alias) {
                $columns_str .= (!is_null($columns_str) ? ', ' : ' ') . (is_int($column) ? $this->addTablePrefix($alias) : ($column != self::COUNT ? $this->addTablePrefix($column) : 'COUNT(*)') . ' AS ' . $alias);
            }
        } else
            $columns_str = ' *';
        return $columns_str;
    }

    private function getTable(?string $table)
    {
        if (is_null($table)) {
            $table = $this->dbi->prefix . $this->table();
        } else {
            $table = $this->dbi->prefix . $table;
        }
        foreach($this->dbi->trusted_tables as $trusted) {
            if ($table == $this->dbi->prefix . $trusted) {
                return $table;
            }
        }
        throw new \Error("Таблица не обслуживается приложением");
    }

    public function all(array $columns = [], string $table = null, bool $distinct = false): self
    {
        $this->query = 'SELECT' . ($distinct ? ' DISTINCT' : '') . $this->columnsToString($columns) . ' FROM ' . $this->getTable($table);
        return $this;
    }

    public function first(array $columns = [], string $table = null): self
    {
        $this->query = 'SELECT' . $this->columnsToString($columns) . ' FROM ' . $this->getTable($table) . ' LIMIT 1';
        return $this;
    }

    public function last(array $columns = [], string $table = null): self
    {
        $this->query = 'SELECT' . $this->columnsToString($columns) . ' FROM ' . $this->getTable($table) . ' LIMIT 1 DESC';
        return $this;
    }

    public function insert(array $values, string $table = null): void
    {
        $insert_data = "";
        foreach ($values as $column => $value) {
            $insert_data .= (!empty($insert_data) ? ", " : "") . "$column = '$value'";
        }
        $this->query = 'INSERT INTO ' . $this->getTable($table) . ' SET ' . $insert_data;
        $this->execute();
    }

    public function update(array $values, string $table = null): self
    {
        $update_data = "";
        foreach ($values as $column => $value) {
            $update_data .= (!empty($update_data) ? ", " : "") . "$column = '$value'";
        }
        $this->query = 'UPDATE ' . $this->getTable($table) . ' SET ' . $update_data;
        return $this;
    }

    public function delete(string $table = null): self
    {
        $this->query = 'DELETE FROM ' . $this->getTable($table);
        return $this;
    }

    public function join(string $table, array $on, string $mode = self::INNER): self
    {
        $on_key = array_key_first($on);
        $this->query .= ' ' . $mode . 'JOIN ' . $this->getTable($table) . ' ON ' . $this->dbi->prefix . $on_key . ' = ' . $this->dbi->prefix . $on[$on_key];
        return $this;
    }

    public function where(string $column, string $value, string $sign = '='): self
    {
        $this->query .= (!str_contains($this->query, "WHERE") ? " WHERE " : " ") . $this->addTablePrefix($column) . " $sign '$value'";
        return $this;
    }

    public function add(string $add_str): self
    {
        $this->query .= ($add_str != self::RIGHT_QUOTE ? '' : ' ') . $add_str;
        return $this;
    }

    public function orderBy(string $column, string $mode = ""): self
    {
        $this->query .= " ORDER BY " . $this->addTablePrefix($column) . "$mode";
        return $this;
    }

    public function groupBy(array $columns): self
    {
        foreach ($columns as $key => $column) {
            $this->query .= (!str_contains($this->query, "GROUP BY") ? " GROUP BY " : ", ") . $this->addTablePrefix($column);
        }
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->query .= " LIMIT $limit";
        return $this;
    }

    public function offset(int $offset): void
    {
        $this->query .= " OFFSET $offset";
    }

    public function getLastInsertId(): string
    {
        return $this->db->lastInsertId();
    }

    public function rowsCount(string $query = null): int
    {
        return count($this->getRowsAsArray($query));
    }

    public function checkData(array $params): bool
    {
        $query = "";
        foreach ($params as $column => $data) {
            if ($column == 'password') {
                $data = SafetyManager::encryptPassword($data);
            }
            $query .= (!empty($query) ? ' AND ' : ' WHERE ') . "$column = '$data''";
        }
        if ($this->rowsCount($query)) {
            return true;
        }
        return false;
    }

    abstract public function table();

}
