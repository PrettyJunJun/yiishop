<?php
namespace backend\controllers;

use backend\models\Goods;
use yii\data\Pagination;
use yii\web\Controller;

class GoodsController extends Controller{
    //>>显示
    public function actionIndex(){
        //>>查询数据
        //总页数
        $query = Goods::find();
        //>>分页工具
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 3
        ]);
        $article = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['goods'=>$article,'pager' => $pager]);
    }

    //>>添加
    public function actionAdd(){
        $model = new Goods();
        if (){

        }
    }
}