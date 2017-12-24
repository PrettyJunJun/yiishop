<?php

namespace backend\models;

use yii\db\ActiveRecord;

class Goods extends ActiveRecord
{
    public function rules()
    {
        return [
            [['name', 'logo', 'goods_category_id', 'brand_id', 'market_price', 'shop_price', 'stock', 'is_on_sale', 'status', 'sort'], 'required'],
            [['market_price', 'shop_price', 'stock',], 'double']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '商品名',
            'logo' => 'LOGO图片',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在销',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }
}