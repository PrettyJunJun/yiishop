<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord{
    //>>上传文件
    public $imgFile;
    //>>指定规则
    public function rules()
    {
        return [
            //>>不能为空
            [['name','intro','sort','status'],'required'],
            //上传文件的验证规则
            ['imgFile', 'file', 'extensions' => ['jpg', 'png', 'gif','jpeg'], 'maxSize' => 1024 * 1024, 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'品牌名称',
            'intro'=>'品牌简介',
            'logo'=>'品牌LOGO',
            'sort'=>'排序',
            'status'=>'状态'
        ];
    }
}