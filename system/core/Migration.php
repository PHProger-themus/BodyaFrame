<?php

namespace system\core;

class Migration extends DB
{

    private string $column = "";
    private array $foreigns = [];

    protected function createTable(string $name, array $columns)
    {
        $this->query = "CREATE TABLE IF NOT EXISTS $name (" . implode(', ', $columns);

        if (!empty($this->foreigns)) {
            foreach ($this->foreigns as $column => $reference) {
                $table = array_key_first($reference);
                $this->query .= ", FOREIGN KEY ($column) REFERENCES {$this->dbi->prefix}$table ($reference[$table])";
            }
        }

        $this->query .= ");";
        $this->execute();

    }

    protected function dropTable(string ...$tables)
    {
        $this->query = "DROP TABLE " . implode(', ', $tables) . ";";
        $this->execute();
    }

    protected function int(int $size = 255)
    {
        $this->column = "INT($size)";
        return $this;
    }

    protected function tinyint(int $size = 3)
    {
        $this->column = "INT($size)";
        return $this;
    }

    protected function varchar(int $size = 255)
    {
        $this->column = "VARCHAR($size)";
        return $this;
    }

    protected function decimal(int $precision = 10, int $scale = 0)
    {
        $this->column = "DECIMAL($precision, $scale)";
        return $this;
    }

    protected function date()
    {
        $this->column = "DATE()";
        return $this;
    }

    protected function time()
    {
        $this->column = "TIME()";
        return $this;
    }

    protected function timestamp()
    {
        $this->column = "TIMESTAMP()";
        return $this;
    }

    protected function double(int $precision = 10, int $scale = 0)
    {
        $this->column = "DOUBLE($precision, $scale)";
        return $this;
    }

    protected function json()
    {
        $this->column = "JSON";
        return $this;
    }

    protected function text()
    {
        $this->column = "TEXT";
        return $this;
    }

    protected function boolean()
    {
        $this->column = "BOOLEAN";
        return $this;
    }

    protected function column(string $type, array $parameters = [])
    {
        $this->column = "$type" . (!empty($parameters) ? "(" . implode(', ', $parameters) . ")" : "");
        return $this;
    }

    protected function notNull()
    {
        $this->column .= " NOT NULL";
        return $this;
    }

    protected function default(string|int|bool $value)
    {
        $this->column .= " DEFAULT " . (is_string($value) ? "\"$value\"" : $value);
        return $this;
    }

    protected function unsigned()
    {
        $this->column .= " UNSIGNED";
        return $this;
    }

    protected function comment(string $comment)
    {
        $this->column .= " COMMENT \"$comment\"";
        return $this;
    }

    protected function autoIncrement()
    {
        $this->column .= " AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    protected function primaryKey()
    {
        $this->column .= " PRIMARY KEY";
        return $this;
    }

    protected function unique()
    {
        $this->column .= " UNIQUE";
        return $this;
    }

    protected function name(string $name, array $foreign_key = [])
    {
        if (!empty($foreign_key)) {
            $this->foreigns[$name] = $foreign_key;
        }
        return "$name $this->column";
    }

    protected function addQuery(string $query)
    {
        return $query;
    }

}