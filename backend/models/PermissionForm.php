<?php
namespace backend\models;

use yii\base\Model;
use yii\rbac\Permission;

class PermissionForm extends Model{
    //>>定义字段
    public $name;
    public $description;

    //>>定义规则
    public function rules()
    {
        return [
            [['name','description'],'required'],
        ];
    }

    public function save(){
        $authManager = \Yii::$app->authManager;
        //>>创建一个权限
        $permission = new Permission();
        //>>权限和路由（操作）对应，方便权限验证
        $permission->name = $this->name;
        $permission->description = $this->description;
        //>>保存
        $authManager->add($permission);
    }

    public function attributeLabels()
    {
        return [
            'name'=>'名称(路由)',
            'description'=>'描述'
        ];
    }
}