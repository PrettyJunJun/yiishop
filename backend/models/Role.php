<?php
namespace backend\models;

use yii\base\Model;

class Role extends Model{
    //>>定义字段
    public $name;
    public $description;
    public $permission;

    //>>定义规则
    public function rules()
    {
        return [
            [['name','description'],'required'],
            //>>权限规则
            ['permission','default','value'=>null]
        ];
    }


    public function attributeLabels()
    {
        return [
            'name'=>'角色名称',
            'description'=>'描述',
            'permission'=>'添加权限'
        ];
    }
}