<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use common\models\SphinxClient;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class GoodsController extends Controller
{
    public $enableCsrfValidation = false;

    //首页显示
    public function actionIndex()
    {
        //>>搜索
        $request = \Yii::$app->request;
        $form = Goods::find();
        //>>接受表单传过来的值
        $sn = empty($request->get('sn')) ? '' : $request->get('sn');
        $name = empty($request->get('name')) ? '' : $request->get('name');
        $price_max = empty($request->get('price_max')) ? '' : $request->get('price_max');
        $price_min = empty($request->get('price_min')) ? '' : $request->get('price_min');
        if ($sn) {
            $form->Where(['like', 'sn', $sn]);
        }
        if ($name) {
            $form->andWhere(['like', 'name', $name]);
        }
        if ($price_max) {
            $form->andWhere(['>', 'shop_price', $price_max]);
        }
        if ($price_min) {
            $form->andWhere(['<', 'shop_price', $price_min]);
        }
        //>>分页工具
        $pager = new Pagination([
            'totalCount' => $form->count(),
            'defaultPageSize' => 5
        ]);
        $rows = $form->andWhere(['>=', 'status', 0])->orderBy('sn asc')->limit($pager->limit)->offset($pager->offset)->all();
        $barend = Brand::find()->all();
        $goods_category = GoodsCategory::find()->all();

        //>>定义一个空数组
        $value = [];
        //>>遍历输出brand表 把它的id作为它的键  把name作为值
        foreach ($barend as $barends) {
            $value[$barends->id] = $barends->name;
        }
//        var_dump($v);die;
        //>>遍历输出goods_category表  把它的id作为键 把name作为值
        $category = [];
        foreach ($goods_category as $goods) {
            $category[$goods->id] = $goods->name;
        }
//        var_dump($f);die;
        return $this->render('index', ['rows' => $rows, 'value' => $value, 'category' => $category, 'pager' => $pager]);
    }

    //添加
    public function actionAdd()
    {
        //商品表
        $model = new Goods();
        $request = new Request();
        //详情表
        $content = new GoodsIntro();
        if ($request->isPost) {
            $model->load($request->post());
            $content->load($request->post());
            if ($model->validate() && $content->validate()) {
                //>>保存添加时间
                $time = date('Y-m-d', time());
                $count = GoodsDayCount::find()->where(['day' => $time])->one();
                //判断是添加还是修改
                if ($count) {
                    //>>如果是添加 把当前时间和添加商品加1
                    $count->count += 1;
                } else {
                    $count = new GoodsDayCount();
                    $count->day = $time;
                    $count->count = 1;
                }
                $count->save();
                //获取时间
                $model->create_time = time();
                //>>生成当前日期和编号后四位
                $model->sn = date('Ymd') . str_pad($count->count, 4, 0, STR_PAD_LEFT);
                $model->save();
                $content->goods_id = $model->id;
                $content->save();
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['goods/index']);
            }
        } else {
            $barend = Brand::find()->all();
            //>>定义一个空数组
            $values = [];
            //>>遍历输出barend表  把id作为键 把name作为值
            foreach ($barend as $barends) {
                $values[$barends->id] = $barends->name;
            }
            return $this->render('add', ['model' => $model, 'values' => $values, 'content' => $content]);
        }
    }

    //修改
    public function actionEdit($id)
    {
        //商品
        $model = Goods::findOne(['id' => $id]);
        //详情
        $content = GoodsIntro::findOne(['goods_id' => $id]);
        $request = new Request();
        if ($request->isPost) {
            $model->load($request->post());
            $content->load($request->post());
            //>>验证goods和goodsintro传过来的数据
            if ($model->validate() && $content->validate()) {
                $model->save();
                $content->save();
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['goods/index']);
            }
        } else {
            $barend = Brand::find()->all();
            //>>定义空数组
            $values = [];
            //>>遍历遍历barend表 把id作为键 把name作为值
            foreach ($barend as $barends) {
                $values[$barends->id] = $barends->name;
            }
            return $this->render('add', ['model' => $model, 'values' => $values, 'content' => $content]);
        }
    }

    //相册
    public function actionGallery($id)
    {
        //>>根据id查询出的所有数据
        $model = GoodsGallery::find()->where(['goods_id' => $id])->all();
        return $this->render('gallery', ['model' => $model, 'id' => $id]);
    }

    //保存图片
    public function actionGalleryAdd()
    {
        $result = \Yii::$app->request;
        if ($result->isPost) {
            $gallery = new GoodsGallery();
            $gallery->goods_id = $result->post('id');
            $gallery->path = $result->post('resu');
            $gallery->save();
            $id = \Yii::$app->db->getLastInsertID();
            return Json::encode(['id' => $id]);
        }

    }

    //相册删除
    public function actionGalleryDelete($id)
    {
        //>>根据id删除对应数据
        $result = GoodsGallery::deleteAll(['id' => $id]);
        //>>使用json
        echo json_encode($result);
    }

    //预览
    public function actionPreview($id)
    {
        //>>根据对应id查询出对应数据
        $coutent = GoodsIntro::find()->where(['goods_id' => $id])->all();
        $gallery = GoodsGallery::find()->where(['goods_id' => $id])->all();
        return $this->render('preview', ['content' => $coutent, 'gallery' => $gallery]);
    }

    //处理图片
    public function actionUploader()
    {
        //实例化图片对象
        $img = UploadedFile::getInstanceByName('file');
        $file = '/upload/goods/' . uniqid() . '.' . $img->extension;
        //如果图片上传成功就保存
        if ($img->saveAs(\Yii::getAlias('@webroot') . $file)) {
            //========================七牛云=================//
            //>>AK
            $accessKey = "KvQGKBBVS3A3EsHB0bkRD4C8f9VPz-K6lC4xplSr";
            //>>SK
            $secretKey = "MznndrSpB1-GdAXOeOyztSr5PJ-9L38MFDCGBhdK";
            //>>空间名称
            $bucket = "prettyboy";
            //>>七牛云地址
            $domain = 'p1ax7h9uq.bkt.clouddn.com';
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot') . $file;
            // 上传到七牛后保存的文件名
            $key = $file;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            //echo "\n====> putFile result: \n";
            if ($err !== null) {
                //失败
                var_dump($err);
            } else {
                //成功
                $url = "http://{$domain}/{$key}";
                echo Json::encode(['url' => $url]);
            }
            //========================七牛云=================//
        } else {
            echo json_encode(false);
        }
    }

    //富文本编辑器
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

    //删除
    public function actionDelete($id)
    {
        //>>查询对应id,根据id删除对应数据
        $result = Goods::deleteAll(['id' => $id]);
        GoodsIntro::deleteAll(['goods_id' => $id]);
        echo json_encode($result);
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