<?php

namespace frontend\controllers;

use backend\models\LoginForm;
use backend\models\Password;
use backend\models\User;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
    //>>关闭跨站请求攻击验证
    public $enableCsrfValidation = false;

    //>>初始化方法
    public function init()
    {
        parent::init();
        //>>设置数据响应的格式为json
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    //>>用户注册
    public function actionUserRegister()
    {
        if (\Yii::$app->request->isPost) {
            $result = [
                "error_code" => 1,
                "msg" => '注册成功',
                "data" => []
            ];
            $user = new User();
            $user->username = \Yii::$app->request->post('username');
            $user->password_hash = \Yii::$app->security->generatePasswordHash('password');
            $user->email = \Yii::$app->request->post('email');
            if ($user->validate()) {
                $user->save();
                //>>注册成功
                $result['error_code'] = 0;
                $result['msg'] = '注册成功';
            } else {
                //>>注册失败
                $result['msg'] = $user->getErrors();
            }
        } else {
            $result['msg'] = '请使用POST请求';
        }
        return $result;

    }

    //>>用户登录
    public function actionLogin()
    {
        if (\Yii::$app->request->isPost) {
            $result = [
                "error_code" => 1,
                "msg" => '登录成功',
                "data" => []
            ];
            $model = new LoginForm();
            $model->username = \Yii::$app->request->post('username');
            $model->password_hash = \Yii::$app->request->post('password');
            if ($model->login()) {
                $session = \Yii::$app->user;
                User::updateAll(['last_login_time' => date('Y-m-d H:i:s', time()), 'last_login_ip' => $_SERVER['REMOTE_ADDR']], ['id' => $session->id]);
                //>>登录成功
                $result['error_code'] = 0;
                $result['msg'] = '登录成功';
            } else {
                //>>登录失败
                $result['msg'] = $model->getErrors();
            }
        } else {
            $result['msg'] = '请使用POST请求';
        }
        return $result;
    }

    //>>修改密码
    public function actionModify()
    {
        if (\Yii::$app->request->isPost) {
            $result = [
                "error_code" => 1,
                "msg" => '修改成功',
                "data" => []
            ];
            $user = User::findOne(['id' => 51]);
            $model = new Password();
            $model->oldpassword = \Yii::$app->request->post('oldpassword');
            $model->newpassword = \Yii::$app->request->post('newpassword');
            $model->confirm = \Yii::$app->request->post('confirm');
            if ($model->validate()) {
                $model->oldpassword = \Yii::$app->security->generatePasswordHash($model->newpassword);
                $user->password_hash = $model->oldpassword;
                $user->save();
                //>>注册成功
                $result['error_code'] = 0;
                $result['msg'] = '修改成功';
            } else {
                //>>注册失败
                $result['msg'] = $model->getErrors();
            }
        } else {
            $result['mag'] = '请使用POST请求';
        }
        return $result;
    }
}