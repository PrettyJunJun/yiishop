<?php

namespace frontend\controllers;

use backend\models\GoodsCategory;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\SignatureHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    //>>商品列表
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength' => 4,
                'maxLength' => 4,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $parent_id = GoodsCategory::find()->where(['parent_id' => 0])->all();
        $rows = [];
        //>>获取顶级分类id
        foreach ($parent_id as $parent) {
            $goods = GoodsCategory::find()->where(['parent_id' => $parent->id])->all();
            //>>根据顶级分类的id保存二级分类
            $rows[$parent->id] = $goods;
            foreach ($goods as $good) {
                $Category = GoodsCategory::find()->where(['parent_id' => $good->id])->all();
                //>>根据二级分类的id把三级分类保存
                $Categorys[$good->id] = $Category;
            }
        }
//        var_dump(Yii::$app->user->isGuest);die;
        return $this->render('index', ['parent_id' => $parent_id, 'goods' => $goods, 'rows' => $rows, 'Categorys' => $Categorys]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

//    public function actionRegist(){
//
//        return $this->renderPartial('regist');
//    }

//    //>>验证用户名唯一
//    public function actionValidateUser($username){
//        if ($username == 'admin'){
//            //>>已存在
//            echo 'false';
//        }else{
//            //>>不存在
//            echo 'true';
//        }
//    }
    //>>阿里云短信
    public function actionAliyun($phone)
    {
        //>>使用正则 验证电话号码
        if (preg_match("/^1[34578]{1}\d{9}$/", $phone)) {
            $code = rand(10000, 99999);
            $res = \Yii::$app->sms->send($phone, ['code' => $code]);
            if ($res->Code == 'OK') {
                //>>发送成功
                //>>将验证码存入redis
                $redis = new \Redis();
                $redis->connect('127.0.0.1');
                $redis->set('code_' . $phone, $code, 300);
                return 'true';
            } else {
                //>>发送失败
                return '手机号码格式错误';
            }
//            $params = array ();
//
//            // *** 需用户填写部分 ***
//
//            // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
//            $accessKeyId = "LTAI1DAmqPQWcyTq";
//            $accessKeySecret = "AqBcHOPOlfW1WLf2zncagPYYZWDHOq";
//
//            // fixme 必填: 短信接收号码
//            $params["PhoneNumbers"] = "18783807924";
//
//            // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
//            $params["SignName"] = "峻峻超市";
//
//            // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
//            $params["TemplateCode"] = "SMS_120120330";
//
//            // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
//            $params['TemplateParam'] = Array (
//                "code" => rand(1000,9999),
////                "product" => "阿里通信"
//            );
//
//            // fixme 可选: 设置发送短信流水号
////            $params['OutId'] = "12345";
//
//            // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
////            $params['SmsUpExtendCode'] = "1234567";
//
//
//            // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
//            if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
//                $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
//            }
//
//            // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
//            $helper = new SignatureHelper();
//
//            // 此处可能会抛出异常，注意catch
//            $content = $helper->request(
//                $accessKeyId,
//                $accessKeySecret,
//                "dysmsapi.aliyuncs.com",
//                array_merge($params, array(
//                    "RegionId" => "cn-hangzhou",
//                    "Action" => "SendSms",
//                    "Version" => "2017-05-25",
//                ))
//            );
//
//            var_dump($content);
        }
    }
}
