<?php

namespace frontend\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    public $address_id;
    //>>定义一个静态属性 ---> 送货方式
    public static $delivery = [
        1 => ['顺丰快递', 25, '送货速度快,服务好,价格贵'],
        2 => ['圆通快递', 20, '速度一般,服务一般,价格贵'],
        3 => ['EMS', 18, '速度快,服务态度好,价格一般'],
        4 => ['申通快递', 18, '速度慢,服务一般,价格一般']
    ];

    //>>定义一个静态属性 ---> 付款方式
    public static $payment = [
        1 => ['货到付款', '	送货上门后再收款，支持现金、POS机刷卡'],
        2 => ['在线支付', '即时到帐，支持银行借记卡、银行信用卡、支付宝、微信支付'],
        3 => ['上门自提', '	自提时付款，支持现金、POS刷卡、支付宝、微信支付'],
    ];

    public function rules()
    {
        return [
            [['name','province','city','area','address','tel','delivery_id','delivery_name','delivery_price','payment_id','payment_name','total','status','create_time','address_id'],'required']
        ];
    }
}