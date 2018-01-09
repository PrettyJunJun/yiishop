<?php

namespace frontend\controllers;

use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
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
    public function actionSearch()
    {
        $name = $_GET['goods_name'];
        $query = Goods::find();
        //>>根据ID找到商品信息
        $goods = $query->where(['like', 'name', $name])->all();
//        var_dump($value);die;
        return $this->render('index', ['goods' => $goods]);
    }
}