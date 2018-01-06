<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Cart extends ActiveRecord{

    public function rules()
    {
        return [
            [['amount','goods_id','member_id'],'required']
        ];
    }
}