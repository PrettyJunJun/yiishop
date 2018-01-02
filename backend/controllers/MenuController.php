<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;

use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class MenuController extends Controller
{
    public function actionIndex()
    {
        $model = Menu::find()->all();

        return $this->render('index', ['model' => $model]);
    }

    //>>添加
    public function actionAdd()
    {
        $model = new Menu();
        $request = new Request();
        $authManager = \Yii::$app->authManager;
        $menu = ArrayHelper::map($authManager->getPermissions(), 'name', 'name');
        //>>根据id 获取上级菜单
        $menu_id = ArrayHelper::map($model->getMenu(), 'id', 'name');
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save();
                //>>设置跳转信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //>>跳转
                return $this->redirect(['menu/index']);
            }
        }
        return $this->render('add', ['model' => $model, 'menu' => $menu, 'menu_id' => $menu_id]);
    }

    //>>修改
    public function actionEdit($id)
    {
        $model = Menu::findOne(['id' => $id]);
        $request = new Request();
        $authManager = \Yii::$app->authManager;
        $menu = ArrayHelper::map($authManager->getPermissions(), 'name', 'name');
        //根据id获取上级菜单
        $menu_id = ArrayHelper::map($model->getMenu(), 'id', 'name');
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save();
                //>>提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //>>跳转页面
                return $this->redirect(['menu/index']);
            } else {
                //>>失败打印错误信息
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model, 'menu' => $menu, 'menu_id' => $menu_id]);
    }

    //>>删除
    public function actionDelete($id)
    {
        Menu::findOne(['id' => $id])->delete();
        \Yii::$app->session->setFlash('success', '删除成功');
        return $this->redirect(['menu/index']);
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