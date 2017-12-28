<?php

namespace backend\models;

use yii\base\Model;

class Role extends Model
{
    //>>定义字段
    public $name;
    public $description;
    public $permission;

    //>>定义场景  常量
    const SCENARIO_ADD_PERMISSION = 'add'; //>>添加权限场景
    const SCENARIO_EDIT_PERMISSION = 'edit'; //>>修改权限场景

    //>>定义规则
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            //>>权限名唯一
            ['name', 'only', 'on' => [self::SCENARIO_ADD_PERMISSION]],//该验证规则只在SCENARIO_ADD_PERMISSION场景生效
            //>>修改权限
            ['name', 'validateName', 'on' => self::SCENARIO_EDIT_PERMISSION],
            //>>权限规则
            ['permission', 'default', 'value' => null]
        ];
    }

    public function only()
    {
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getRole($this->name);
        if ($permission) {
            $this->addError('name', '角色已存在');
        }
    }

    public function validateName()
    {
        $authManager = \Yii::$app->authManager;
        //>>名称是否修改
        $names = \Yii::$app->request->get('name');
        //>>如果修改 而且新名称已经存在 提示错误
        if ($names != $this->name) {
            $name = $authManager->getRole($this->name);
            if ($name) {
                $this->addError('name', '名称已存在');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => '角色名称',
            'description' => '描述',
            'permission' => '添加权限'
        ];
    }
}