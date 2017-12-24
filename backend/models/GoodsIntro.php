<?php
namespace backend\models;

use yii\db\ActiveRecord;

class GoodsIntro extends ActiveRecord{
    public static function primaryKey()
    {
        return [
            'goods_id'
        ];
    }

    public function rules()
    {
        return [
            ['content','required']
        ];

    }
    public function attributeLabels()
    {
        return [
            'content'=>'详情',
        ];
    }
}
