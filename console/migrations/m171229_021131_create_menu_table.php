<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171229_021131_create_menu_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->string(50)->comment('菜单名称'),
            'superior'=>$this->string()->comment('上级菜单'),
            'url'=>$this->string()->comment('路由'),
            'sort'=>$this->integer()->comment('排序'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
