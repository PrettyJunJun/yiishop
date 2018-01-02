<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\GoodsCategory;
use yii\data\Pagination;
use yii\web\Request;

class GoodsCategoryController extends \yii\web\Controller
{
    //>>显示页面
    public function actionIndex()
    {
        //>>总条数
        $query = GoodsCategory::find();
        //>>分页工具
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 10
        ]);
        $article = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index', ['goods_category' => $article, 'pager' => $pager]);
    }

    //>>添加
    public function actionAdd()
    {
//        $model = new GoodsCategory();
//        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
//            if ($model->parent_id) {
//                //>>创建子节点
////            $russia = new Menu(['name' => 'Russia']);
//                $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
//                $model->appendTo($parent);
//            } else {
//                //>>创建根节点
////            $countries = new Menu(['name' => 'Countries']);
//                $model->makeRoot();
////            $model->save();
//            }
//            //>>设置提示信息
//            \Yii::$app->session->setFlash('success', '添加成功');
//            return $this->redirect(['goods-category/index']);
//        }
//        return $this->render('add', ['model' => $model]);
        $model = new GoodsCategory();
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                if ($model->parent_id) {
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    $model->appendTo($parent);
                } else {
                    $model->makeRoot();
                }
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['goods-category/index']);
            }
        } else {
            return $this->render('add', ['model' => $model]);
        }
    }

    //>>修改
    public function actionEdit($id)
    {
//        $request = new Request();
//        //>>回显
//        $model = GoodsCategory::findOne(['id' => $id]);
//        if ($request->isPost) {
//            $model->load($request->post());
//            if ($model->validate()) {
//                //>>保存
//                $model->save();
//                //>>设置提示信息
//                \Yii::$app->session->setFlash('success', '修改成功');
//                //>>跳转页面
//                return $this->redirect(['goods-category/index']);
//            } else {
//                //>>验证失败后打印错误信息
//                var_dump($model->getErrors());
//            }
//        }
//        return $this->render('add', ['model' => $model]);
        $model = GoodsCategory::find()->where(['id' => $id])->one();
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            if ($model->validate()) {
                if ($model->parent_id) {
                    $parent = GoodsCategory::findOne(['id' => $model->parent_id]);
                    $model->appendTo($parent);
                } else {
                    $model->parent_id = 0;
                    $model->makeRoot();
                }
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['goods-category/index']);
            }
        } else {
            return $this->render('add', ['model' => $model]);
        }
    }

    //>>测试ztree
    public function actionZtree()
    {
        return $this->renderPartial('ztree');
    }

    //>>删除
    public function actionDelete($id)
    {
        if (GoodsCategory::findOne(['parent_id' => $id])) {
            //存在子分类就不删除
            echo json_encode(false);
        } else {
            //不存在就删除
            $r = GoodsCategory::deleteAll(['id' => $id]);
            echo json_encode($r);
        }
    }

//    public function behaviors()
//    {
//        return [
//            'rbac'=>[
//                'class'=>RbacFilter::className()
//            ]
//        ];
//    }
}
