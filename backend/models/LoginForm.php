<?php
namespace backend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password_hash;
    public $code;

    //>>指定规则
    public function rules()
    {
        return [
            [['username','password_hash','code'],'required'],
            //验证码
            ['code', 'captcha', 'captchaAction' => 'user/captcha'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password_hash'=>'密码',
            'code'=>'验证码'
        ];
    }

    //登录方法
    public function login()
    {
        //>>验证账号和密码
        $admin = User::findOne(['username' => $this->username]);
        if ($admin) {
            //>>用户名存在 验证密码
            if (\Yii::$app->security->validatePassword($this->password_hash, $admin->password_hash)) {
                //>>密码正确
                //>>将用户信息保存到session中
                \Yii::$app->user->login($admin);
                return true;
            } else {
                //>>提示信息
                $this->addError('password_hash', '密码不正确');
            }
        } else {
            //>>用户名不存在
            //>>提示信息
            $this->addError('username', '用户名不存在');
        }
        return false;
    }
}