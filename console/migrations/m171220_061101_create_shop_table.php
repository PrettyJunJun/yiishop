<?php

use yii\db\Migration;

/**
 * Handles the creation of table `shop`.
 */
class m171220_061101_create_shop_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey()->notNull()->comment('品牌ID'),
            'name'=> $this->string(50)->notNull()->comment('品牌名称'),
            'intro'=> $this->text()->notNull()->comment('品牌简介'),
            'logo'=> $this->string(255)->notNull()->comment('LOGO图片'),
            'sort'=> $this->integer(11)->comment('排序'),
            'status'=> $this->integer(2)->comment('状态(-1删除 0隐藏 1正常)')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
