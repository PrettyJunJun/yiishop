<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order_goods`.
 */
class m180106_025447_create_order_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('order_goods', [
            'id' => $this->primaryKey(),
            'order_id'=>$this->integer()->comment('订单ID'),
            'goods_id'=>$this->integer()->comment('商品ID'),
            'goods_name'=>$this->string(255)->comment('商品名称'),
            'logo'=>$this->string(255)->comment('图片'),
            'price'=>$this->decimal()->comment('价格'),
            'amount'=>$this->integer()->comment('数量'),
            'total'=>$this->decimal()->comment('小计'),
            'member_id'=>$this->integer()->comment('用户id'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order_goods');
    }
}
