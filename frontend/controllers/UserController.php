<?php

namespace frontend\controllers;

use frontend\models\Address;
use frontend\models\DetailAddress;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller
{
    public $enableCsrfValidation = false;

    //>>验证用户名是否存在
    public function actionUsername($username)
    {
        $res = Member::find()->where(['username' => $username])->one();
        if ($res) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    //用户注册
    public function actionRegist()
    {
        $request = new Request();
        $model = new Member();
        if ($request->isPost) {
            //>>把load第二个参数设置为空
            $model->load($request->post(), '');
            if ($model->validate()) {
                //添加成功对密码进行加密
                $model->password_hash = \Yii::$app->security->generatePasswordHash($model->password_hash);
                //创建用户的当前时间
                $model->created_at = time();
                //默认选择1
                $model->status = 1;
                $model->save(false);
                echo "注册成功";
                sleep(1);
                //跳转到登录界面
                return $this->redirect(['user/login']);
            }
        }
        return $this->render('regist');
    }

    //登录
    public function actionLogin()
    {
        $request = new Request();
        $model = new LoginForm();
        if ($request->isPost) {
            $model->load($request->post(), '');
            if ($model->validate()) {
                if ($model->login()) {
                    $user = Member::find()->where(['username' => $model->username])->one();
                    //>>用户最后登录时间
                    $user->last_login_time = time();
                    //>>用户最后登录IP
                    $user->last_login_ip = $_SERVER['REMOTE_ADDR'];
                    $user->save(false);
                    echo '登录成功';
                    sleep(1);
                    return $this->redirect('http://www.yiishop.com');
                }
            }
        }
        return $this->render('login');
    }

    //>>收货地址
    public function actionAddress($id)
    {

        //>>详情地址
        $addresses = Address::find()->where(['member_id' => $id])->all();
        $request = \Yii::$app->request;
        foreach ($addresses as &$address) {
            $detail = DetailAddress::find()->where(['address_id' => $address->id])->asArray()->one();
            $address['detail_address'] = $detail['detail_address'];
        }
        if ($request->isPost) {
            $address = new Address();
            $detail = new DetailAddress();
            $address->load($request->post(), '');
            if (!isset($request->post()['status'])) {
                $address->status = 0;
            }
            if ($address->validate()) {
                $address->member_id = $id;
                if ($address->status == 1) {
                    $row = Address::find()->where(['member_id' => \Yii::$app->user->identity->id])->andWhere(['status' => 1])->one();
                    $row->status = 0;
                    $row->save(false);
                }

                $address->save(false);
                $detail->address_id = $address->id;
                $detail->detail_address = $address->detail_address;
                $detail->save(false);
                return $this->redirect(['user/address', 'id' => \Yii::$app->user->identity->id]);
            }
        }

        return $this->render('add', ['addresses' => $addresses]);
    }

    //>>收货地址修改
    public function actionEdit($id)
    {
        $addresses = Address::find()->where(['member_id' => \Yii::$app->user->identity->id])->all();
        foreach ($addresses as &$address) {
            $detail1 = DetailAddress::find()->where(['address_id' => $address->id])->asArray()->one();
            $address['detail_address'] = $detail1['detail_address'];
        }
        $request = \Yii::$app->request;
        $addr = Address::find()->where(['id' => $id])->one();
        $detail = DetailAddress::find()->where(['address_id' => $addr->id])->one();
        if ($request->isPost) {

            $addr->load($request->post(), '');

            if (!isset($request->post()['status'])) {
                $addr->status = 0;
            }
            if ($addr->validate()) {
                $detail->detail_address = $addr->detail_address;

                if ($addr->status == 1) {
                    $address = Address::find()->where(['member_id' => \Yii::$app->user->identity->id])->andWhere(['status' => 1])->one();
                    $address->status = 0;
                    $address->save(false);
                }
                $detail->save(false);

                $addr->save(false);
                return $this->redirect(['user/address', 'id' => \Yii::$app->user->identity->id]);
            }
        }

        return $this->render('edit', ['addr' => $addr, 'detail' => $detail, 'addresses' => $addresses]);
    }

    //>>收货地址删除
    public function actionDelete($id)
    {
        $address = Address::find()->where(['id' => $id])->one();
        $detail = DetailAddress::find()->where(['address_id' => $address->id])->one();
        $row = $address->delete();
        $rows = $detail->delete();
        if ($row && $rows) {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }

    //>>设置收货地址的状态
    public function actionStatus($id)
    {
        $addresses = Address::find()->where(['member_id' => \Yii::$app->user->identity->id])->all();
        foreach ($addresses as $address) {
            $address->status = 0;
            $address->save(false);
        }
        $addr = Address::find()->where(['id' => $id])->one();
        $addr->status = 1;
        $addr->save(false);
        return $this->redirect(['user/address', 'id' => \Yii::$app->user->identity->id]);
    }

    //>>注销
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
}