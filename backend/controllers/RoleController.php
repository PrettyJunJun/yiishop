<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Role;
use function PHPSTORM_META\type;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class RoleController extends Controller
{
    //>>显示页面
    public function actionIndex()
    {
        $authManager = \Yii::$app->authManager;
        //>>查询角色
        $authManager = $authManager->getRoles();
        return $this->render('index', ['authManager' => $authManager]);
    }

    //>>角色权限添加
    public function actionAdd()
    {
        $model = new Role();
        //>>获取所有的权限
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermissions();
        $role = ArrayHelper::map($permission, 'name', 'description');
        //>>指定当前场景 如果没有指定就是默认场景
        $model->scenario = Role::SCENARIO_ADD_PERMISSION;
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                //>>创建角色
                $role = new \yii\rbac\Role();
                $role->name = $model->name;
                $role->description = $model->description;
                $authManager->add($role);
                //>>添加权限
                $permissions = $model->permission;
                foreach ($permissions as $permission) {
                    $permission = $authManager->getPermission($permission);
                    $authManager->addChild($role, $permission);
                    //>>提示信息
                    \Yii::$app->session->setFlash('success', '添加成功');
                    return $this->redirect(['role/index']);
                }

            }
        }
        return $this->render('add', ['model' => $model, 'role' => $role]);
    }

    //>>角色修改
    public function actionEdit($name)
    {

        $authManager = \Yii::$app->authManager;
        //>>从数据表获取权限
        // $permission = $authManager->getRole($name);
        //>>/获取所有权限
        $permission = $authManager->getPermissions();
        $role = ArrayHelper::map($permission, 'name', 'description');
        $model = new Role();
        $rows = $authManager->getRole($name);
        $model->scenario = Role::SCENARIO_EDIT_PERMISSION;
        //>>赋值回显
        $model->name = $rows->name;
        $model->description = $rows->description;
        //>>/获取角色关联的权限
        $permissions = $authManager->getPermissionsByRole($name);
        $model->permission = [];
        foreach ($permissions as $permission) {
            $model->permission[] = $permission->name;
        }
        //>>post保存
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                //>>创建角色
                $rows->name = $model->name;
                $rows->description = $model->description;
                $authManager->update($name, $rows);
                //>>去除角色关联的所有权限
                $authManager->removeChildren($rows);
                //>>重新关联权限
                foreach ($model->permission as $permissionName) {
                    $permissions = $authManager->getPermission($permissionName);
                    $authManager->addChild($rows, $permissions);
                }
                //>>提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //>>跳转回首页
                return $this->redirect(['role/index']);
            }
        }

        return $this->render('add', ['model' => $model, 'role' => $role]);
    }

    //>>角色删除
    public function actionDelete($name)
    {
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getRole($name);
        $authManager->remove($permission);
        //>>提示信息
//        \Yii::$app->session->setFlash('success','删除成功');
//        return $this->redirect(['role/index']);
    }

//    public function behaviors()
//    {
//        return [
//            'rbac'=>[
//                'class'=>RbacFilter::className()
//            ]
//        ];
//    }
}