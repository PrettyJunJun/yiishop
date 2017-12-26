<?php

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\data\Pagination;
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
        $request = new Request();
        $model = new User();
        if ($request->isPost) {
            //>>加载表单数据
            $model->load($request->post());
            if ($model->validate()) {
                //>>对密码进行加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                //>>保存
                $model->save(false);
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //>>跳转首页
                $this->redirect(['user/index']);
            } else {
                //>>失败后打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            return $this->render('add', ['model' => $model]);
        }
    }

    //>>修改
    public function actionEdit($id)
    {
        $request = new Request();
        //>.回显
        $model = User::findOne(['id' => $id]);
        if ($request->isPost) {
            $model->load($request->post());
                if ($model->verifpwd()){
//                    var_dump($model);die;
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                    //>>保存
                    $model->save(false);
                    //>>设置提示信息
                    \Yii::$app->session->setFlash('success', '修改成功');
                    //>跳转首页
                    return $this->redirect(['user/index']);
                }else{
                    $model->addError('newpassword','密码不一致');
                }

        } else {
            $model->password_hash = '';
            return $this->render('password', ['model' => $model]);
        }
    }

    //>>删除
    public function actionDelete($id){
        User::findOne(['id'=>$id])->delete();
        //>>提示信息
        \Yii::$app->session->setFlash('success','删除成功');
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
    public function actionLogin(){
        //>>登录表单
        $model = new LoginForm();
        //>>接受表单提交的数据
        $request = \Yii::$app->request;
        if ($request->isPost){
            $model->load($request->post());
            if ($model->login()) {
                //>>最后登录时间和IP
                $session = \Yii::$app->user;
                User::updateAll(['last_login_time' => date('Y-m-d H:i:s', time()), 'last_login_ip' => $_SERVER['REMOTE_ADDR']], ['id' => $session->id]);
                //>>提示信息
                \Yii::$app->session->setFlash('success', '登录成功');
                //>>跳转
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