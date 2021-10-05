<?php

namespace console\migrations;

use system\core\Migration;

class m_211004191501_aa extends Migration
{

    public function up()
    {
        $this->addColumns('pages', [
            $this->json()->notNull()->after('description')->name('json_data'),
        ]);
    }

    public function down()
    {
        $this->dropColumns('pages', ['json_data']);
    }

}