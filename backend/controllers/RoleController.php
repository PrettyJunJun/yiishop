<?php
namespace backend\controllers;

use backend\models\Role;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class RoleController extends Controller{
    //>>显示页面
    public function actionIndex(){
        $authManager = \Yii::$app->authManager;
        $authManager = $authManager->getPermissions();
        return $this->render('index',['authManager'=>$authManager]);
    }
    //>>角色权限添加
    public function actionAdd(){
        $model = new Role();
        //>>获取所有的权限
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermissions();
        $roles = ArrayHelper::map($permission,'name','description');
        $request = new Request();
        if ($request->isPost){
            $model->load($request->post());
            if ($model->validate()){
                //>>创建角色
                $role = new \yii\rbac\Role();
                $role->name=$model->name;
                $role->description=$model->description;
                $authManager->add($role);
                //>>添加权限
                $permissions = $model->permission;
                foreach ($permissions as $permission){
                   $permission =   $authManager->getPermission($permission);
                   $authManager->addChild($role,$permission);
                   //>>提示信息
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['role/index']);
                }

            }
        }
        return $this->render('add',['model'=>$model,'roles'=>$roles]);
    }
}