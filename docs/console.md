# Console

### Contents
- [Commands](#Commands)
    - [create](#create)
    - [file](#file)
    - [migrate](#migrate)
    - [rollback](#rollback)

Console can help you to do any stuff automatically using cron, or create framework entities such as Controllers, Models, migration files.
## Commands
### create
Powerful command for creating BodyaFrame entities.

```bash
php run create *entity* *name*
```

List of entities that you can create:

Entity | \*entity\*
--- | ---
Controller | controller
Console Controller | consoleController
Model | model
Console Model | consoleModel
View file | view
Content files | content
Rule | rule
Migration file | migration

Name must be typed without extension.

This command will create new files in their folders. You can specify namespace for files also using slash:

```bash
php run create controller user/account
```

This will create `app\controllers\user\Account` controller.

While creating content files, namespaces will only be applied to controllers, all the view files will be created in the `views` directory, so make sure that you don't want to create content file with existing name, otherwise new file will replace the old one. This will be fixed in the future.

When creating view files, command also will create language files for all the languages if configuration's option `multilang` is enabled.

### file

Execute console controller. After you created Console Controller using command above, add some code to it and type:

```bash
php run file *controller*/*action*
```

This will execute controller.

In the controller you can use models and invoke special console methods. For example, `$this->red("Error")`, which will return red `"Error"` text and you can `echo` it to the console. Red, green, yellow, blue, magenta, cyan and grey colors are available. Moreover, you can also set background color using:
```php
$this->color("Some text", "foreground color", "background color")
```
Background color can be omitted. All the listed colors can be used here, foreground color can be white or black also, and background can be black (value by default).

You can execute console command in the controller, using:
```php
$this->execute("php run create migration my_new_migration")
```

### migrate

Used for applying migration files. More info in [Migrations](./migrations.md) section.

### rollback

Used to rollback migration files. More info in [Migrations](./migrations.md) section.