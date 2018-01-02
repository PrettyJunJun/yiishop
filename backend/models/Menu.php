<?php
namespace backend\models;

use yii\base\Model;
use yii\db\ActiveRecord;

class Menu extends ActiveRecord {

    //>>顶级菜单
    public $top;

    public function rules()
    {
        return [
            [['name','parent_id','sort'],'required'],
            ['url', 'default', 'value' => null]
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'菜单名称',
            'parent_id'=>'上级菜单',
            'url'=>'地址/路由',
            'sort'=>'状态'
        ];
    }

    //静态方法 获取上级菜单
    public static function getMenu()
    {
        $menu = self::find()->select(['id', 'parent_id', 'name'])->asArray()->all();
        array_unshift($menu, ['id' => 0, 'parent_id' => 0, 'name' => '顶级菜单']);
        return $menu;
    }

}