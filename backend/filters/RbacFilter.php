<?php

namespace backend\filters;


use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilter extends ActionFilter
{
    //>>操作执行之前
    public function beforeAction($action)
    {
        //>>判断用户是否有权限
//        return \Yii::$app->user->can($action->uniqueId);
        if (!\Yii::$app->user->can($action->uniqueId)) {
            //>>如果用户没有登录 显示登录页面
            if (\Yii::$app->user->isGuest) {
                //>>跳转到登录页面
                return $action->controller->redirect(\Yii::$app->user->loginUrl)->send();
            }
            throw new HttpException(403, '对不起您没有该操作权限!');
        }
        return true;

    }
}