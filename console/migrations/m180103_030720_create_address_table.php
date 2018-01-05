<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m180103_030720_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(20)->comment('收货人'),
            'province'=>$this->string()->comment('省份'),
            'city'=>$this->string()->comment('城市'),
            'area'=>$this->string()->comment('地区'),
            'detailed'=>$this->string()->comment('详细地址'),
            'phone'=>$this->string()->comment('手机号码'),
            'status'=>$this->integer()->comment('选中状态'),
            'member_id'=>$this->integer()->comment('关联用户'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
