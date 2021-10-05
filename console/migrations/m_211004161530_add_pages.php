<?php

namespace console\migrations;

use system\core\Migration;

class m_211004161530_add_pages extends Migration
{

    public function up()
    {
        $this->createTable('pages', [
            $this->int()->notNull()->autoIncrement()->comment("Page id")->name('id'),
            $this->varchar(20)->notNull()->default('not assigned')->unique()->comment("Page title")->name('title'),
            $this->varchar()->notNull()->comment("Page description")->name('description'),
            $this->int(5)->notNull()->comment("Seo URL link")->name('link', ['routes' => 'id']),
            $this->addQuery("FULLTEXT (title)")
        ]);
    }

    public function down()
    {
        $this->dropTable('pages');
    }

}