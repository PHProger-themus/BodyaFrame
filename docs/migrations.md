# Migrations

### Contents
- [Creating Migration](#Creating-Migration)
- [Managing migrations](#Managing-migrations)
  - [Managing tables](#Managing-tables)
  - [Managing columns](#Managing-columns)
- [Applying migrations](#Applying-migrations)
## Creating Migration
Go to console and type: 
```bash
php run create migration *name*
``` 
Specify name and press Enter. A new migration file appeared in `console/migrations` folder.

## Managing migrations
New migration file contains next code:
```php
<?php

namespace console\migrations;

use system\core\Migration;

class m_211004191501_example extends Migration
{

    public function up()
    {
        // Migrate
    }

    public function down()
    {
        // Rollback
    }

}
```

`up()` method is used for migrating while `down()` method is used for rollback. In my simple migration system you can only create, drop tables and create, drop and alter columns. It's possible to perform any query inside one of these methods using `$this->execute("*query*")`.

### Managing tables

To create table, use `$this->createTable()` method:

```php
$this->createTable('table', [/* ... */]);
```
First argument is new table name, second is columns array. To add columns, you can use several methods:

- Defining column begins with specifying column data type. Use `int()`, `tinyInt()`, `varchar()`, `decimal()`, `date()`, `time()`, `timestamp()`, `json()`, `double()`, `text()` and `boolean()` methods on `$this` to start defining column. There are methods for defining frequently used data types, but if you want to define another data type, use `$this->column("*type*", [/* params */])`.
- All the methods above return `$this` variable, so you can define column using methods chain.
- After declaring data type continue adding extra data:
  - `notNull()`
  - `default("*value*")`
  - `unsigned()`
  - `comment("*comment*")`
  - `autoIncrement()`
  - `primaryKey()`
  - `unique()`
- And finally last method must be `name("*column name*", [/* foreign keys */])`. First argument is column name, second is foreign keys array, which must be declared like following:
```php
[
    "foreign_table" => "foreign_column", // link current column with *foreign_colimn* of *foreign_table*
    // ...
]
```
You can also add extra queries using `$this->addQuery("*query*")`.

Creating table example:
```php
$this->createTable('table', [
    $this->int()->notNull()->autoIncrement()->comment("Table id")->name('t_id'),
    $this->varchar(20)->notNull()->default('not assigned')->unique()->comment("Table title")->name('t_title'),
    $this->text()->notNull()->comment("Table description")->name('t_description'),
    $this->int(5)->notNull()->comment("Table link")->name('t_link', ['another' => 'a_id']),
    $this->addQuery("FULLTEXT (t_description)")
]);
```
To drop table(s), use method:
```php
$this->dropTable('table', ...);
```
### Managing columns

To add column(s) to existing table, use method:
```php
$this->addColumns('table', [
    // ...
);
```
Defining columns is similar as in table creating instructions.

To alter column(s), use method:
```php
$this->alterColumns('table', [
    // ...
);
```
Name of the column must refer to existing column.

To drop column(s), use method:
```php
$this->dropColumns('table', ['column', ...]);
```

To rename column(s), use method:
```php
$this->renameColumn('table', 'old_column_name', 'new_column_name');
```

### Applying migrations
Now we can apply migration. Go to console and type: 
```bash
php run migrate
```
All the migrations will be applied. You can specify which migrations must be applied:
```bash
php run migrate 211004191501_example, ...
```
To rollback migrations, use this:
```bash
php run rollback
```
You can also specify, which migrations are needed in rollback:
```bash
php run rollback 211004191501_example, ...
```