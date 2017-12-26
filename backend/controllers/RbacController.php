<?php

namespace backend\controllers;

use backend\models\PermissionForm;
use yii\rbac\Permission;
use yii\web\Controller;
use yii\web\Request;

class RbacController extends Controller
{
    //>>显示页面
    public function actionIndex()
    {
        $authManager = \Yii::$app->authManager;
        $authManager = $authManager->getPermissions();

        return $this->render('index', ['authManager' => $authManager]);
    }

    //>>添加权限
    public function actionAddPermission()
    {
        $model = new PermissionForm();
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save();
                //>>提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['rbac/index']);
            }
        }
        return $this->render('add-permission', ['model' => $model]);
    }

    //>>修改权限
    public function actionEdit($name)
    {
        $authManager = \Yii::$app->authManager;
        //>>从数据表获取权限
        $permission = $authManager->getPermission($name);
        $names = $permission->name;
        $model = new PermissionForm();
        $model->name = $permission->name;
        $model->description = $permission->description;
        //>>post保存
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $permission->name = $model->name;
                $permission->description = $model->description;
                $authManager->update($names, $permission);
                //>>提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //>>跳转回首页
                return $this->redirect(['rbac/index']);
            }
        }

        return $this->render('add-permission', ['model' => $model]);
    }

    //>>权限删除
    public function actionDelete($name)
    {
        $authManager = \Yii::$app->authManager;
        $permission = $authManager->getPermission($name);
        $authManager->remove($permission);
        //>>提示信息
        \Yii::$app->session->setFlash('success', '删除成功');
        //>>跳转页面
        return $this->redirect(['rbac/index']);
    }
}