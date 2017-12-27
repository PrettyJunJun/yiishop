<?php

namespace backend\models;

use yii\base\Model;

class Password extends Model
{
    //>>定义字段
    //>>新密码
    public $newpassword;
    //>>旧密码
    public $oldpassword;
    //>>确认密码
    public $confirm;

    //>>定义规则
    public function rules()
    {
        return [
            [['newpassword', 'oldpassword', 'confirm'], 'validateRe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'oldpassword' => '旧密码',
            'newpassword' => '新密码',
            'confirm' => '确认密码',
        ];
    }

    //>>验证密码规则
    public function validateRe()
    {
        if ($this->oldpassword) {
            //>>判断是否填写旧密码
            $id = \Yii::$app->request->get('id');
            $password = User::findOne(['id' => $id]);
            if (\Yii::$app->security->validatePassword($this->oldpassword, $password->password_hash)) {
                if (!$this->newpassword) {
                    $this->addError('newpassword', '新密码不能为空');
                } elseif (!$this->confirm) {
                    //>>确认密码不能为空
                    $this->addError('confirm', '确认密码不能为空');
                } elseif ($this->newpassword && $this->confirm) {
                    //>>两次密码是否一致
                    if ($this->newpassword !== $this->confirm) {
                        $this->addError('newpassword', '新密码和确认密码不一致');
                    }
                }
            }

        } else {
            //>>如果没有填写旧密码 就不修改
            $this->addError('oldpassword', '旧密码不能为空');
        }
    }

}