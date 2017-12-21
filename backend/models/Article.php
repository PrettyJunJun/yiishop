<?php

namespace backend\models;

use yii\db\ActiveRecord;

class Article extends ActiveRecord
{

    public $content;

    //>>指定规则
    public function rules()
    {
        return [
            //>>不能为空
            [['name', 'intro', 'article_category_id', 'sort', 'status', 'content'], 'required'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '文章名称',
            'intro' => '文件简介',
            'article_category_id' => '文章分类ID',
            'sort' => '文章排序',
            'status' => '状态',
            'create_time' => '创建时间',
            'content' => '文章内容'
        ];
    }

    //>>关联两张表
    public function getArticleCategory(){
        return $this->hasMany(ArticleCategory::className(),['id'=>'article_id']);
    }
}