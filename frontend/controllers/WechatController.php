<?php
/**
 * 微信开发
 */

namespace frontend\controllers;

use Codeception\Module\Redis;
use EasyWeChat\Message\News;
use frontend\models\LoginForm;
use frontend\models\Member;
use yii\helpers\Url;
use yii\web\Controller;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;


class WechatController extends Controller
{
    //>>关闭csrf验证
    public $enableCsrfValidation = false;

    //>>和微信服务器交互
    public function actionIndex()
    {
        $app = new Application(\Yii::$app->params['wechat']);

        // 从项目实例中得到服务端应用实例。
        //>>根据用户的位置查询商家位置

        $server = $app->server;

        $server->setMessageHandler(function ($message) {
            //setMessageHandler
            switch ($message->MsgType) {
                case 'event':
                    //>>判断是否是点击事件
                    if ($message->Event == 'CLICK') {
                        //return '你点了卡布奇洛';
                        switch ($message->EventKey) {
                            case 'kbql':
                                return '你点了卡布奇洛';
                                break;
                            case 'zan':
                                return '你点赞了';
                                break;

//                            default:
//                                return $message->EventKey;
                        }
                    }
                    return '收到事件消息';
                    break;
                case 'text':
//                    return '收到文字消息';
//                    return $message->Content;
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1');
                    //>>去redis查询是否有用户的信息
                    if ($redis->exists('location_' . $message->FromUserName)) {
                        //>>有位置信息
                        $location = $redis->hGetAll('location_' . $message->FromUserName);
                        //$message->Content;
                        $url = "http://api.map.baidu.com/place/v2/search?query={$message->Content}&location={$location['x']},{$location['y']}&radius=2000&output=json&ak=4KWC2jo0oGV6vdaiK8b4vs9ciwwx4axl&page_size=8&scope=2";

//                        return $url;
                        $json_str = file_get_contents($url);
                        $data = json_decode($json_str);
                        $datas = [];
                        $images = [
                            'http://www.jlds110.com/include/ueditor/php/upload/20141114/14159484283229.png',
                            'http://img4.imgtn.bdimg.com/it/u=3664076263,1661045131&fm=27&gp=0.jpg',
                            'http://img1.touxiang.cn/uploads/20140416/16-055606_921.jpg',
                            'http://imgsrc.baidu.com/forum/w%3D580/sign=6eb563fa462309f7e76fad1a420f0c39/537e2cf33a87e95014d5850210385343faf2b418.jpg',
                            'http://www.qqzhi.com/uploadpic/2015-01-13/011542217.jpg'
                        ];
                        foreach ($data->results as $result) {
                            $news = new News([
                                'title' => $result->name,
                                'url' => $result->detail_info->detail_url,
                                'image' => $images[rand(0, 5)],
                            ]);
                            $datas[] = $news;
                        }
                        return $datas;
                        //>>回复图文消息
//                        $news = new News([
//                            'title'       => '星巴克',
//                            'description' => '给小姐姐倒杯卡布奇洛',
//                            'url'         => 'https://www.starbucks.com.cn/',
//                            'image'       => 'http://www.jlds110.com/include/ueditor/php/upload/20141114/14159484283229.png',
//                        ]);
//                        return $news;
                    } else {
                        //>>没有位置信息
                        return '请先发送您的位置信息!!!';
                    }
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    //>>将用户的位置保存到redis (经度 纬度 地址)
//                    $message->Location_Y;
//                    $message->Location_X;
//                    $message->Label;
//                    $message->FromUserName;
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1');
                    $redis->hSet('location_' . $message->FromUserName, 'x', $message->Location_X);
                    $redis->hMset('location_' . $message->FromUserName, [
                        'x' => $message->Location_X,
                        'y' => $message->Location_Y,
                        'label' => $message->Label,
                    ]);
                    return '已收到您的位置,请发送查询关键字';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });

        $response = $server->serve();

        $response->send(); // Laravel 里请使用：return $response;
    }

    //>>微信菜单
    public function actionSetMenu()
    {

        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $buttons = [
            [
                "type" => "view",
                "name" => "在线商城",
                "url" => "http://www.prettyjun.cn/"
            ],
            [
                "type" => "view",
                "name" => "最新活动",
                "url" => "http://www.prettyjun.cn/"
            ],
            [
                "name" => "个人中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "我的订单",
                        "url" => "http://www.prettyjun.cn/"
                    ],
                    [
                        "type" => "view",
                        "name" => "绑定账号",
                        "url" => "http://www.prettyjun.cn/"
                    ]
                ],
            ],
        ];
        $menu->add($buttons);
        echo "设置菜单成功";
    }

    //>>查询菜单
    public function actionGetMenu()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $menu = $app->menu;
        $menus = $menu->all();
        var_dump($menus);
    }

    //>>测试页面
    public function actionTest()
    {
        //>>获取用户openid
        $app = new Application(\Yii::$app->params['wechat']);
//        1、引导用户进入授权页面同意授权，获取code
        $response = $app->oauth->scopes(['snsapi_userinfo'])->redirect();
        $response->send();

    }

    //>>授权回调页
    public function actionCallback()
    {
//        2、通过code换取网页授权access_token（与基础支持中的access_token不同）
//        3、如果需要，开发者可以刷新网页授权access_token，避免过期
//        4、通过网页授权access_token和openid获取用户基本信息（支持UnionID机制）
        $app = new Application(\Yii::$app->params['wechat']);
        $user = $app->oauth->user();
        //>>将openid保存到session
        \Yii::$app->session->set('openid', $user->getId());
        return $this->redirect(['wechat/login']);
//        var_dump($user);
// $user 可以用的方法:
// $user->getId();  // 对应微信的 OPENID
// $user->getNickname(); // 对应微信的 nickname
// $user->getName(); // 对应微信的 nickname
// $user->getAvatar(); // 头像网址
// $user->getOriginal(); // 原始API返回的结果
// $user->getToken(); // access_token， 比如用于地址共享时使用
    }

    //>>我的订单
    public function actionOrder()
    {
        //>>判断用户登录状态
        //>>如果未登录 引导用户登录(绑定账号)
        if (\Yii::$app->user->isGuest) {
            Url::remember(['wechat/order'], 'redirect');
            return $this->redirect(['wechat/login']);
        }

        echo "显示我的订单";
    }

    public function actionLogin()
    {
        //>>获取用户openid
        if (\Yii::$app->session->has('openid')) {
            $openid = \Yii::$app->session->get('openid');
        } else {
            $app = new Application(\Yii::$app->params['wechat']);
            $response = $app->oauth->redirect();
            $response->send();

        }
        //>>检测openid是否绑定账号,如果用户已经绑定就自动登录,未绑定就让用户绑定账号
        $user = Member::find()->where(['openid' => $openid])->one();
        if ($user) {
            //>>用户已绑定账号
//            $member = Member::findOne(['id' => $uid]);
            \Yii::$app->user->login($user);
            //>>取出之前记住的网址
//            $url = Url::previous('redirect');
            return $this->redirect(['watche/order']);
        }

        //>>显示登录表单
        $model = new LoginForm();
        if (\Yii::$app->request->isPost) {
            $model->load(\Yii::$app->request->isPost);
            $model->validate();
            if ($model->login()) {
                //>>验证账号密码
                $user = Member::findOne(['username' => $model->username]);
                $redis->hSet('openid', $openid, $user->id);
                //>>跳转页面
                $url = Url::previous('redirect');
                return $this->redirect($url);
            }

        }

        return $this->render('login', ['model' => $model]);
    }

    //>>解除绑定
    public function actionRelieve()
    {
        //>>判断用户是否登录
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['watche/login']);
        } else {
            Member::updateAll(['openid' => 0], ['id' => \Yii::$app->user->id]);
            return $this->redirect(['watche/login']);
        }
    }

    //>>微信消息推送
    public function actionMsg()
    {
        $app = new Application(\Yii::$app->params['wechat']);
        $notice = $app->notice;
        $messageId =
            $notice->
            //>>发送给谁
            to('oN1Tc0oV7XV_X8qazaUgHJLKWsZQ')->
            //>>模板ID
            uses('xHX3n8RO8lA30NEZVBc4q11kfNyVMbfYsLrN9rn05BI')->
            //>>订单列表的URL
            andUrl(Url::to(['wechat/order'], 1))->
            //>>模板参数
            data(['msg' => 'XXX,您的订单我们已经收到,请在5分钟内支付,逾期我们将取消订单!!!'])->
            send();
        var_dump($messageId);
    }
}