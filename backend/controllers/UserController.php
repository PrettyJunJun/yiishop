<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\LoginForm;
use backend\models\Password;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller
{
    public $enableCsrfValidation = false;

    //>>显示页面
    public function actionIndex()
    {
        $query = User::find();
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 3
        ]);
        $model = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index', ['model' => $model, 'pager' => $pager]);
    }

    //>>添加
    public function actionAdd()
    {
        $model = new User();
        //>>获取所有角色
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();
        //>>把name作为键 description作为值
        $authManagers = ArrayHelper::map($roles, 'name', 'description');
        $request = new Request();
        if ($request->isPost) {
            //>>加载表单数据
            $model->load($request->post());
            if ($model->validate()) {
                //>>添加时对密码进行加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                //>>保存
                $model->save(false);
                //>>给用户添加角色
                if ($model->roles) {
                    foreach ($model->roles as $rows) {
                        $name = $authManager->getRole($rows);
                        $authManager->assign($name, $model->id);
                    }
                }
//                var_dump($model->roles);die;
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //>>跳转首页
                return $this->redirect(['user/index']);
            } else {
                //>>失败后打印错误信息
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model, 'authManagers' => $authManagers]);
    }

    //>>修改
    public function actionEdit($id)
    {
        //>.回显
        $model = User::findOne(['id' => $id]);
        //>>修改用户和角色
        $authManager = \Yii::$app->authManager;
        $roles = $authManager->getRoles();
        $authManagers = ArrayHelper::map($roles, 'name', 'description');
        //>>定义空数组
        $rows = [];
        //>>遍历出所有角色
        $name = $authManager->getRolesByUser($id);
        foreach ($name as $role) {
            $rows[] = $role->name;
        }
        $model->roles = $rows;
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                $model->save(false);
                //>>修改清除所有角色
                $authManager->revokeAll($id);
                //>>给用户添加角色
                if ($model->roles) {
                    foreach ($model->roles as $role) {
                        $role = $authManager->getRole($role);
                        $authManager->assign($role, $id);
                    }
                }
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //>跳转首页
                return $this->redirect(['user/index']);
            } else {
                //>>失败后打印错误信息
                var_dump($model->getErrors());
            }

        } else {
            return $this->render('add', ['model' => $model, 'authManagers' => $authManagers]);
        }
    }

    //>>修改密码
    public function actionModify()
    {

        $id = \Yii::$app->user->identity->id;
        $user = User::findOne(['id' => $id]);
        $model = new Password();
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
//                var_dump($model);die;
                $model->oldpassword = \Yii::$app->security->generatePasswordHash($model->newpassword);
                $user->password_hash = $model->oldpassword;
                $user->save(false);
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '修改成功');

                //$model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                //>跳转首页
                return $this->redirect(['user/index']);
            } else {
                //>>失败后打印错误信息
                var_dump($model->getErrors());
            };
        };
        return $this->render('password', ['model' => $model]);
    }

    //>>删除
    public function actionDelete($id)
    {
        User::findOne(['id' => $id])->delete();
        //>>提示信息
        \Yii::$app->session->setFlash('success', '删除成功');
        //>>跳转页面
        return $this->redirect(['user/index']);
    }

    //>>验证码
    public function actions()
    {
        return [
            'captcha' => [
                'class' => CaptchaAction::className(),
                'height' => 34,
                'minLength' => 4,
                'maxLength' => 4
            ]
        ];
    }

    //>>登录
    public function actionLogin()
    {
        //>>登录表单
        $model = new LoginForm();
        //>>接受表单提交的数据
        $request = \Yii::$app->request;
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->login()) {
                //>>最后登录时间和IP
                $session = \Yii::$app->user;
                User::updateAll(['last_login_time' => date('Y-m-d H:i:s', time()), 'last_login_ip' => $_SERVER['REMOTE_ADDR']], ['id' => $session->id]);
                //>>提示信息
                \Yii::$app->session->setFlash('success', '登录成功');
                //>>跳转首页
                return $this->redirect(['user/index']);
            }
        }
        return $this->render('login', ['model' => $model]);
    }

    //>>注销登录
    public function actionLogout()
    {
        \Yii::$app->user->logout();

        return $this->redirect(['user/login']);
    }


}