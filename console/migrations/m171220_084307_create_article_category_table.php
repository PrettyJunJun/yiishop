<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m171220_084307_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
            'name'=> $this->string(50)->notNull()->comment('文章名称'),
            'intro'=>$this->text()->notNull()->comment('文章简介'),
            'sort'=>$this->integer(11)->notNull()->comment('文章排序'),
            'status'=>$this->integer(2)->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
