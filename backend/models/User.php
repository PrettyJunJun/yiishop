<?php

namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    //>>验证码
    public $code;
    //>>新密码
    public $newpassword;
    //>>旧密码
    public $oldpassword;
    //>>确认密码
    public $confirm;
    //>>添加用户权限
    public $roles;

    //>>指定规则
    public function rules()
    {
        return [
            // 'password_hash',, 'status'
            [['username', 'email'], 'required'],
            [['roles'], 'default', 'value' => null],
            [['email'], 'unique'],
            [['newpassword', 'oldpassword', 'confirm'], 'validateRe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password_hash' => '密码',
            'oldpassword' => '旧密码',
            'newpassword' => '新密码',
            'confirm' => '确认密码',
            'email' => '邮箱',
            'status' => '状态',
            'roles' => '添加角色'

        ];
    }

    //>>验证密码规则
    public function validateRe()
    {
        if ($this->oldpassword) {
            //>>判断是否填写旧密码
            if (!$this->newpassword) {
                $this->addError('newpassword', '新密码不能为空');
            } elseif (!$this->confirm) {
                //>>确认密码不能为空
                $this->addError('confirm', '确认密码不能为空');
            } elseif ($this->newpassword && $this->confirm) {
                //>>两次密码是否一致
                if ($this->newpassword !== $this->confirm) {
                    $this->addError('newpassword', '新密码和确认密码不一致');
                } else {
                    $password = \Yii::$app->security->validatePassword($this->oldpassword, $this->password_hash);
                    if (!$password) {
                        $this->addError('oldpassword', '旧密码填写错误');
                    }
                }
            }
        } else {
            //>>如果没有填写旧密码 就不修改
            $this->addError('oldpassword', '旧密码不能为空');
        }
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    //>>添加成功在表中生成和存储每个用户的认证密钥
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = \Yii::$app->security->generateRandomString();
            }
            return true;
        }
        return false;
    }

    //>>获取菜单
    public function getMenus()
    {
        $menuItems = [];
        //>>获取所有父级菜单
        $menus = Menu::find()->where(['parent_id' => 0])->all();
        foreach ($menus as $menu) {
            //>>获取子分类
            $children = Menu::find()->where(['parent_id' => $menu->id])->all();
            $items = [];
            foreach ($children as $child) {
                //>>判断用户是否有显示菜单的权限
                if (\Yii::$app->user->can($child->url)){
                    $items[] = ['label' => $child->name, 'url' => [$child->url]];
                }
            }
//            $items = [
//                ['label'=>'','url'=>['']]
//            ];
            //>>没有子菜单的不需要显示
            if ($items){
                $menuItems[] = ['label' => $menu->name, 'items' => $items];
            }
        }
//        $items = [
//            ['label'=>'','url'=>['']]
//        ];
//        $menuItems[] = ['label'=>'','items'=>$items];
        return $menuItems;
    }
}