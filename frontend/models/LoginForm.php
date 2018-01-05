<?php

namespace frontend\models;

use yii\base\Model;

class LoginForm extends Model
{
    //>>用户名
    public $username;
    //>>密码
    public $password;
    //>>确认密码
    public $remember;

    //定义规则
    public function rules()
    {
        return [
            [
                ['username', 'password'], 'required'
            ],
            ['remember', 'default', 'value' => null]
        ];
    }

    //登录验证
    public function login()
    {
        $result = Member::find()->where(['username' => $this->username])->one();
        //验证用户名是否存在
        if ($result) {
            if (\Yii::$app->security->validatePassword($this->password, $result->password_hash)) {
                //是否记住密码
                if ($this->remember == 1) {
                    //>>如果用记住密码 就保存一个过期时间
                    $auth_key = 7 * 24 * 3600;
                } else {
                    //>>没有记住密码  时间就是0
                    $auth_key = 0;
                }
                //>>密码正确
                //>>将用户信息保存到session中
                \Yii::$app->user->login($result, $auth_key);
                return true;
            } else {
                echo '密码错误';
            }
        } else {
            echo '用户名不正确';
        }
        //延时
        sleep(1);
        return false;
    }
}