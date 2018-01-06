<?php

namespace frontend\controllers;

use backend\models\Goods;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\DetailAddress;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Cookie;
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
                    //>>判断用户是否登录,如果登录将cookie信息存入数据表
                    $cookies = \Yii::$app->request->cookies;
                    //>>判断cookie里是否有数据
                    if ($cookies->has('cart')) {
                        $cart_info = unserialize($cookies->getValue('cart'));
                        $good_ids = array_keys($cart_info);
                        //>>获得cookie每个商品id
                        foreach ($good_ids as $good_id) {
                            $count = Cart::find()->where(['goods_id' => $good_id])->andWhere(['member_id' => \Yii::$app->user->identity->id])->one();
                            //>>判断用户数据表中是否有商品id
                            if ($count) {
                                $count->amount += $cart_info[$good_id];
                                $count->save(false);
                            } else {
                                $cart = new Cart();
                                $cart->member_id = \Yii::$app->user->identity->id;
                                $cart->goods_id = $good_id;
                                $cart->amount = $cart_info[$good_id];
                                $cart->save(false);
                            }
                        }
                        $cookies = \Yii::$app->response->cookies;
                        $cookies->remove('cart');
                    }
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

    //>>添加购物车成功页面
    public function actionAddCart($goods_id, $amount)
    {
        //>>判断用户是否登录
        if (\Yii::$app->user->isGuest) {
            //>>如果用户未登陆就将购物车信息保存到cookie
            $cookies = \Yii::$app->request->cookies;
            if ($cookies->has('cart')) {
                $cart = unserialize($cookies->getValue('cart'));
            } else {
                $cart = [];
            }
            //>>判断商品存不存在,不存在就新增,存在就累加
            if (array_key_exists($goods_id, $cart)) {
                $cart[$goods_id] += $amount;
            } else {
                $cart[$goods_id] = $amount;
            }
            //>>写cookie
            $cookies = \Yii::$app->response->cookies;
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($cart);
            //var_dump($cookie);die;
            $cookies->add($cookie);
        } else {
            //>>如果用户已经将这个商品选入购物车
            $name = Cart::find()->where(['goods_id' => $goods_id])->andWhere(['member_id' => \Yii::$app->user->identity->id])->one();
//            var_dump($name);die;
            if ($name) {
                $name->amount += $amount;
                $name->save(false);
            } else {
                //>>登陆后直接将购物车信息存入数据表
                $cart = new Cart();
                $cart->member_id = \Yii::$app->user->identity->id;
                $cart->goods_id = $goods_id;
                $cart->amount = $amount;
                $cart->save(false);
            }

        }
        //>>跳转到购物车
        return $this->redirect(['user/cart']);
    }

    //>>购物车页面
    public function actionCart()
    {
        //>>判断用户是否登录,未登录数据从cookie中获取
        if (\Yii::$app->user->isGuest) {
            //>>如果用户未登录就从cookie中获取购物车信息
            $cookies = \Yii::$app->request->cookies;
            if ($cookies->has('cart')) {
                // var_dump($cookies->getValue('cart'));die;
                $value = unserialize($cookies->getValue('cart'));
                //$models = Goods::find()->where(['in', 'id', $ids])->all();
            } else {
                $value = [];
            }
            $good_ids = array_keys($value);

        } else {
            //>>登陆后根据登陆用户id从数据库查表获取购物车信息
            $carts = Cart::find()->where(['member_id' => \Yii::$app->user->identity->id])->all();
            $good_ids = [];
            //>>获取所有商品id
            $value = [];
            foreach ($carts as $cart) {
                $good_ids[] = $cart->goods_id;
                $value[$cart->goods_id] = $cart->amount;
            }
            //>>获取所有商品信息
        }
//        var_dump($good_ids);die;
        $models = Goods::find()->where(['in', 'id', $good_ids])->all();
        return $this->render('cart', ['models' => $models, 'value' => $value]);
    }

    //>>购物车信息删除
    public function actionCartDelete($id)
    {
        //>>判断用户是否存在
        if (!\Yii::$app->user->isGuest) {
            //>>查出购物车数据
            $cart = Cart::findOne(['goods_id' => $id]);
            $result = $cart->delete();
            if ($result) {
                echo Json::encode('true');
            } else {
                echo Json::encode('false');
            }
        } else {
            //>>如果用户没有登录，就把cookie中的数据删除
            $cookies = \Yii::$app->request->cookies;
            $carts = $cookies->getValue('cart');
            $carts = unserialize($carts);
            $ids = array_keys($carts);
            foreach ($ids as $value) {
                if ($value == $id) {
                    unset($carts[$value]);
                }
            }
            //>>将新数组重新放回cookie
            $cookies = \Yii::$app->response->cookies;
            //>>把之前的cookie删除
            $cookies->remove('cart');
            $cookie = new Cookie();
            $cookie->name = 'cart';
            $cookie->value = serialize($carts);
            $cookies->add($cookie);
            echo Json::encode('true');
        }
    }

    //>>修改购物车数量的方法
    public function actionChange()
    {
        $g_id = \Yii::$app->request->post('g_id');
        $count = \Yii::$app->request->post('count');
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            if ($cookies->has('cart')) {
                $arr = unserialize($cookies->getValue('cart'));
                foreach ($arr as $id => $c) {
                    if ($id == $g_id) {
                        $arr[$g_id] = $count;
                    }
                }
                var_dump($arr);
                $cookies = \Yii::$app->response->cookies;
                $cookie = new Cookie();
                $cookie->name = 'cart';
                $cookie->value = serialize($arr);
                $cookies->add($cookie);
                return json_encode(true);
            }
        } else {
            $cart = Cart::find()->where(['goods_id' => $g_id])->andWhere(['member_id' => \Yii::$app->user->identity->id])->one();
            $cart->amount = $count;
            $cart->save(false);
            return json_encode(true);
        }
    }

    //>>注销
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->redirect(['user/login']);
    }
}