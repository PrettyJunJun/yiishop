<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Address extends ActiveRecord{
    public $detail_address;
    //>>指定规则
    public function rules()
    {
        return [
            [['name','province','city','area','phone','detail_address'],'required'],
            ['status','default','value'=>null],
        ];
    }
}