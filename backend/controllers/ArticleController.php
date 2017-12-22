<?php

namespace backend\controllers;

use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller
{
    //>>富文本编辑器
    public function actions()
    {
        return [
            'ueditor' => [
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config' => [
                    //上传图片配置
                    'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                    'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }

    //>>显示页面
    public function actionIndex()
    {
        //>>调用模型接受数据
        $article = Article::find()->where(['>=', 'status', 0]);
        //>>分页工具
        $pager = new Pagination([
            'totalCount' => $article->count(),
            'defaultPageSize' => 3
        ]);
        //>>调用模型接受数据
        $Categorys = ArticleCategory::find()->all();
        //>>定义一个数组 将Categorys的name查出来
        $arr = [];
        foreach ($Categorys as $val) {
            $arr[$val['id']] = $val['name'];
        }
//    var_dump($arr);die;
        $article = $article->where(['>=', 'status', 0])->limit($pager->limit)->offset($pager->offset)->all();
        //>>调用视图
        return $this->render('index', ['article' => $article, "arr" => $arr, 'pager' => $pager]);

    }

    //>>添加
    public function actionAdd()
    {
        $detail = new ArticleDetail();
        $request = new Request();
        $model = new Article();
        //>>加载组件

        $category = ArticleCategory::find()->all();
        $options = ArrayHelper::map($category, 'id', 'name');
//        $arr = [];
//        foreach ($Categorys as $val) {
//            $arr[$val['id']] = $val['name'];
//        }
////        var_dump($arr);die;
        if ($request->isPost) {
            //>>加载表单数据
            $model->load($request->post());
            if ($model->validate()) {
                $detail->content = $model->content;
                $detail->save();
                $model->create_time = time();
                //>>保存
                $model->save();
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //>>跳转页面
                return $this->redirect(['article/index']);
            } else {
                //>>验证失败 打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //>>显示页面
            return $this->render('add', ['model' => $model, 'options' => $options]);
        }
    }

    //>>修改
    public function actionEdit($id)
    {
        //>>显示页面
//        var_dump($id);die;
        $request = new Request();
        $model = Article::findOne($id);
        $detail = ArticleDetail::find()->where(['article_id' => $id])->one();
        // var_dump($detail);die;
        $model->content = $detail->content;
//   var_dump($model);die;
        //>>详情页

//        var_dump($model->content);die;
        $category = ArticleCategory::find()->all();
        $options = ArrayHelper::map($category, 'id', 'name');
        if ($request->isPost) {
            $model->load($request->post());
            //>>后台验证
            if ($model->validate()) {
                $detail->content = $model->content;
                $model->save();
                $detail->save();
                //>>提示信息 跳转
                \Yii::$app->session->setFlash('success', '修改成功');
                //>>跳转到首页
                return $this->redirect(['article/index']);
            } else {
                //>>失败打印错误信息
                var_dump($model->getErrors());
            }
        }
        return $this->render('add', ['model' => $model, 'options' => $options]);
    }

    //>>删除
    public function actionDelete($id)
    {
        //>>查找id
        $model = Article::findOne(['id' => $id]);

        $model->updateAttributes(['status' => -1]);
    }
}