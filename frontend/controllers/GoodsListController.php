<?php

namespace frontend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use common\models\SphinxClient;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class GoodsListController extends Controller
{
    public function actionIndex($id)
    {
        //>>判断是二级分类还是三级分类
        $cate = GoodsCategory::findOne(['id' => $id]);
        if ($cate->depth == 2) {
            $ids = [$id];
//            $goods_id = Goods::find()->where(['goods_category_id'=>$id])->all();
        } else {
            $goods_id = $cate->children()->select('id')->andWhere(['depth' => 2])->asArray()->all();
            //>>三级分类
//            $goods_id = Goods::find()->where(['goods_category_id' => $id])->asArray()->all();
            $ids = ArrayHelper::map($goods_id, 'id', 'id');
//            foreach ($goods_id as $value){
//                $ids[] = $value['id'];
//            }
        }
        $goods = Goods::find()->where(['in', 'goods_category_id', $ids])->all();
        return $this->render('index', ['goods' => $goods]);
    }

    public function actionGoodDisplay($id)
    {
        //>>根据id查询商品详情
        $intro = GoodsIntro::find()->where(['goods_id' => $id])->one();
        //>>商品图片
        $gallerys = GoodsGallery::find()->where(['goods_id' => $id])->all();
        //>>根据id查询商品
        $row = Goods::find()->where(['id' => $id])->one();
        //>>品牌
//        $brand = Brand::find()->where(['id' => $row->brand_id])->one();
//        $row->brand_id = $brand->name;
        $row->view_times = $row->view_times + 1;
        $row->save();
//        var_dump($gallerys);die;
        return $this->render('goods', ['row' => $row, 'intro' => $intro, 'gallerys' => $gallerys]);
    }

    //>>搜索
//    public function actionSearch()
//    {
//        $name = $_GET['goods_name'];
//        $query = Goods::find();
//        //>>根据ID找到商品信息
//        $goods = $query->where(['like', 'name', $name])->all();
////        var_dump($value);die;
//        return $this->render('index', ['goods' => $goods]);
//    }

    //>>商品分词搜索功能
    public function actionSearch()
    {
        $name = \Yii::$app->request->get('goods_name');
        $cl = new SphinxClient();
        $cl->SetServer('127.0.0.1', 9312);
        //$cl->SetServer ( '10.6.0.6', 9312);
        //$cl->SetServer ( '10.6.0.22', 9312);
        //$cl->SetServer ( '10.8.8.2', 9312);
        $cl->SetConnectTimeout(10);
        $cl->SetArrayResult(true);
        //$cl->SetMatchMode ( SPH_MATCH_ANY);
        $cl->SetMatchMode(SPH_MATCH_EXTENDED2);
        $cl->SetLimits(0, 1000);
        $info = $name;//>>查询关键字
        $res = $cl->Query($info, 'mysql');//>>查询用到的索引
        //print_r($cl);
        //print_r($res);
        $ids = [];
        if (isset($res['matches'])) {
            foreach ($res['matches'] as $match) {
                $ids[] = $match['id'];
            }
        }
//        var_dump($ids);
        $total = Goods::find()->where(['in', 'id', $ids])->count();
        $pager = new Pagination([
            'totalCount' => $total,
            'defaultPageSize' => 10
        ]);
        $goods = Goods::find()->limit($pager->limit)->offset($pager->offset)->where(['like', 'name', $info])->all();

        return $this->render('index', ['goods' => $goods, 'pager' => $pager, 'info' => $info]);

    }
}