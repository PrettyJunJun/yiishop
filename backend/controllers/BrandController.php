<?php

namespace backend\controllers;

//>>品牌
use backend\filters\RbacFilter;
use backend\models\ArticleCategory;
use backend\models\Brand;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class BrandController extends Controller
{
    public $enableCsrfValidation = false;

    //>>显示
    public function actionIndex()
    {
        //>>总条数
        $query = Brand::find()->where(['>=', 'status', 0]);
        //>>分页工具
        $pager = new Pagination([
            'totalCount' => $query->count(),
            'defaultPageSize' => 3
        ]);
        $brand = $query->where(['>=', 'status', 0])->limit($pager->limit)->offset($pager->offset)->all();
        //>.调用模型上的方法
//        $brand = Brand::find()->all();
        //>>调用视图
        return $this->render('index', ['brand' => $brand, 'pager' => $pager]);
    }

    //>>添加
    public function actionAdd()
    {
        //>>实例化品牌活动记录
        $model = new Brand();
        //>>加载组件
        $request = new Request();
        if ($request->isPost) {
            //>>加载表单数据
            $model->load($request->post());
            //>>验证前处理图片
//            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
//            var_dump($model->imgFile);die;
            //>>后台验证
            if ($model->validate()) {
//                var_dump($model);die;
                //>>处理图片
//                $file = '/upload/' . uniqid() . '.' . $model->imgFile->extension;

//                if ($model->imgFile->saveAs(\Yii::getAlias('@webroot') . $file)) {
//                    //>>文件上传成功
//                    $model->logo = $file;
//                }
                //>>保存
                $model->save();
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '添加成功');
                //>跳转首页
                return $this->redirect(['brand/index']);
            } else {
                //>>验证失败 打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            //>>显示页面
            return $this->render('add', ['model' => $model]);
        }

    }

    //>>处理ajax上传
    public function actionUpload()
    {
        $img = UploadedFile::getInstanceByName('file');
        $fileName = '/Upload/' . uniqid() . '.' . $img->extension;
        if ($img->saveAs(\Yii::getAlias('@webroot') . $fileName, 0)) {
            //>>上传成功 返回图片地址 方便回显
            //===============上传到七牛云===============//
            // 需要填写你的 Access Key 和 Secret Key
            $accessKey = "KvQGKBBVS3A3EsHB0bkRD4C8f9VPz-K6lC4xplSr";
            $secretKey = "MznndrSpB1-GdAXOeOyztSr5PJ-9L38MFDCGBhdK";
            $bucket = "prettyboy";
            $domian = 'p1ax7h9uq.bkt.clouddn.com';
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            //$fileName = '/upload/1.jpg';
            $filePath = \Yii::getAlias('@webroot') . $fileName;
            // 上传到七牛后保存的文件名
            $key = $fileName;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            //echo "\n====> putFile result: \n";
            if ($err !== null) {
                //var_dump($err);
                return Json::encode(['error' => '上传失败']);
            } else {
                //>>上传成功 图片访问地址 http://<domain>/<key>
                //var_dump($ret);
                $url = "http://{$domian}/{$key}";
                return Json::encode(['url' => $url]);
            }
            //===============上传到七牛云===============//

        } else {
            //>>上传失败
            return Json::encode(['error' => '上传失败']);
        }

    }

    //>>修改
    public function actionEdit($id)
    {
        //>>显示页面
        $request = new Request();
        //>>回显
        $model = Brand::findOne(['id' => $id]);
        if ($request->isPost) {
            $model->load($request->post());
            //>>验证前处理图片
//            $img = UploadedFile::getInstanceByName('file');
//            $model->imgFile = UploadedFile::getInstance($model, 'imgFile');
            if ($model->validate()) {
//                if ($model->imgFile) {
//                    //>>处理图片
//                    $file = '/upload/' . uniqid() . '.' . $model->imgFile->extension;
//                    if ($model->imgFile->saveAs(\Yii::getAlias('@webroot') . $file)) {
//                        //>>文件上传成功
//                        $model->logo = $file;
//                    }
//                }
                //>>保存
                $model->save();
                //>>设置提示信息
                \Yii::$app->session->setFlash('success', '修改成功');
                //跳转页面
                return $this->redirect(['brand/index']);
            } else {
                //>>验证失败后打印错误信息
                var_dump($model->getErrors());
            }
        } else {
            return $this->render('add', ['model' => $model]);
        }
    }

    //>>删除
    public function actionDelete($id)
    {
        //>>查找id
        $model = Brand::findOne(['id' => $id]);
//        $model->status = -1;
        $model->updateAttributes(['status' => -1]);
        //>>提示信息
//        \Yii::$app->session->setFlash('success', '删除成功');
        //>>跳转页面
//        return $this->redirect(['brand/index']);

    }

    //>>处理七牛文件上传
    public function actionQiniu()
    {
        //>>测试七牛云对象储存 文件上传


    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className()
            ]
        ];
    }
}