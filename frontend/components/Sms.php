<?php

namespace frontend\components;

use frontend\models\SignatureHelper;
use yii\base\Component;

class Sms extends Component
{

    //>>定义参数
    public $ak;
    public $sk;
    public $sign;
    public $template;

    public function send($phone, $param)
    {
        $params = [];
        //>>电话号码
        $params["PhoneNumbers"] = $phone;
        //>>模板参数
        $params['TemplateParam'] = $param;

        //>>短信签名
        $params['SignName'] = $this->sign;
        //>>短信模板
        $params["TemplateCode"] = $this->template;

        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $this->ak,
            $this->sk,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );
        return $content;
    }
}