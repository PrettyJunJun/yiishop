<?php

namespace backend\models;

use yii\base\Model;

class LoginForm extends Model
{
    public $username;
    public $password_hash;
    public $code;
    public $remember;

    //>>指定规则
    public function rules()
    {
        return [
            [['username', 'password_hash', 'code'], 'required'],
            //验证码
            ['code', 'captcha', 'captchaAction' => 'user/captcha'],
            ['remember', 'default', 'value' => null]
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password_hash' => '密码',
            'code' => '验证码',
            'remember' => '记住密码'
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
                //>>判断用户是否勾选记住密码
                if ($this->remember == 1) {
                    //>>如果用记住密码 就保存一个过期时间
                    $auth_key = 7 * 24 * 3600;
                } else {
                    //>>没有记住密码  时间就是0
                    $auth_key = 0;
                }
                //>>密码正确
                //>>将用户信息保存到session中
                \Yii::$app->user->login($admin, $auth_key);
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