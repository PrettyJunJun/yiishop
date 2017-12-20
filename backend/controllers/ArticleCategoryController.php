<?php

namespace backend\controllers;

use backend\models\ArticleCategory;
use yii\data\Pagination;
use yii\web\Request;

class ArticleCategoryController extends \yii\web\Controller
{
    //>>显示页面
    public function actionIndex()
    {
        //>>总条数
        $query = ArticleCategory::find()->where(['>=', 'status', 0]);
        //>>分页工具
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 3
        ]);
        //>>调用模型上的方法
//        $article = ArticleCategory::find()->all();
        $article = $query->where(['>=', 'status', 0])->limit($pager->limit)->offset($pager->offset)->all();

        return $this->render('index', ['article_category' => $article, 'pager' => $pager]);
    }

    //>>添加
    public function actionAdd()
    {
        $model = new ArticleCategory();
        //>加载组件
        $request = new Request();
        if ($request->isPost) {
            //>>加载表单数据
            $model->load($request->post());
            //>>后台验证
            if ($model->validate()) {
                //>>保存
                $model->save();
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //>>跳转页面
                return $this->redirect(['article-category/index']);
            } else {
                //>>验证失败 打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //>>显示页面
            return $this->render('add', ['model' => $model]);
        }
    }

    //>>修改
    public function actionEdit($id)
    {
        //>>显示页面
        $request = new Request();
        //>>回显
        $model = ArticleCategory::findOne(['id' => $id]);
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                //>>保存
                $model->save();
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //>>跳转页面
                return $this->redirect(['article-category/index']);
            } else {
                //>>验证失败后打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //>>显示页面
            return $this->render('add', ['model' => $model]);
        }
    }

    //>>删除
    public function actionDelete($id)
    {
        //>>查找id
        $model = ArticleCategory::findOne(['id' => $id]);

        $model->updateAttributes(['status' => -1]);
//        $model->status = -1;
//        $model->save();
        //>>提示信息
//        \Yii::$app->session->setFlash('success', '删除成功');
        //>>跳转页面
//        return $this->redirect(['article-category/index']);

    }
}
