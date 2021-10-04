<?php

namespace console\migrations;

use system\core\Migration;

class migration_tmp_041021191501 extends Migration
{

    public function up()
    {
        $this->createTable('test', [
            $this->int()->notNull()->autoIncrement()->name('id'),
        ]);
    }

    public function down()
    {
        $this->dropTable('test');
    }

}