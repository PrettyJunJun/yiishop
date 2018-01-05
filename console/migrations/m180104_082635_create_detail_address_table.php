<?php

use yii\db\Migration;

/**
 * Handles the creation of table `detail_address`.
 */
class m180104_082635_create_detail_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('detail_address', [
            'id' => $this->primaryKey(),
            'address_id'=>$this->integer()->comment('订单ID'),
            'detail_address'=>$this->string()->comment('地址详细')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('detail_address');
    }
}
