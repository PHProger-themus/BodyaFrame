<?php

namespace console\migrations;

use system\core\Migration;

class m_211005170559_change_column extends Migration
{

    public function up()
    {
        $this->alterColumns('pages', [
            $this->int()->default(0)->after('description')->name('json_data'),
        ]);
        $this->renameColumn('pages', 'json_data', 'nojson_data');
    }

    public function down()
    {
        $this->renameColumn('pages', 'nojson_data', 'json_data');
        $this->alterColumns('pages', [
            $this->json()->after('description')->name('json_data'),
        ]);
    }

}