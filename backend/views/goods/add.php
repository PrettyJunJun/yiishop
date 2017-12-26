<?php
$form = \yii\widgets\ActiveForm::begin();
echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'logo')->hiddenInput();
/**
 * @var $this \yii\web\View
 */
$this->registerJsFile('@web/webuploader/webuploader.js', ['depends' => \yii\web\YiiAsset::className()]);
$this->registerCssFile('@web/webuploader/webuploader.css');
echo <<<html
    <div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <img id="img" src="$model->logo" width="85">
    <div id="filePicker">选择图片</div>
</div>
html;
$url = \yii\helpers\Url::to(['goods/uploader']);
$js = <<<JS
    // 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    //  swf:  '/web/webup/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/gif,image/jpeg,image/png,image/jpg,image/bmp'
    }
});
uploader.on( 'uploadSuccess', function( file,response) {
    console.debug(response['url']);
    $('#img').attr('src',response.url);
    $('#goods-logo').val(response.url);
});
JS;
$this->registerJs($js);
//商品分类
echo $form->field($model, 'goods_category_id')->hiddenInput();
//======================zTree=====================
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js', [
    'depends' => \yii\web\JqueryAsset::className()
]);
$nodes = \backend\models\GoodsCategory::getNodes();
$url = \yii\helpers\Url::to(['goods-category/edit']);
//>>判断goods_category_id是否存在
if ($model->goods_category_id) {
    $id = $model->goods_category_id;
} else {
    $id = 0;
}
$js = <<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode) {
		          //获取的值复制给$('#goodscategory-parent_id')
		          $("#goods-goods_category_id").val(treeNode.id);
		        }
	}
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
       
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //展示所有点
            zTreeObj.expandAll(true);
            //回显
            var node=zTreeObj.getNodeByParam('id',$id,null);//根据id获取节点
            zTreeObj.selectNode(node);
JS;
$this->registerJs($js);
echo <<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;
//======================zTree=====================
//品牌分类
echo $form->field($model, 'brand_id')->dropDownList($values);
echo $form->field($model, 'market_price')->textInput();
echo $form->field($model, 'shop_price')->textInput();
echo $form->field($model, 'stock')->textInput();
echo $form->field($model, 'is_on_sale')->radioList([0 => '下架', 1 => '在销']);
echo $form->field($model, 'status')->radioList([0 => '回收站', 1 => '正常']);
echo $form->field($model, 'sort')->textInput();
echo $form->field($content, 'content')->widget(\common\widgets\ueditor\Ueditor::className());
echo '<button class="btn btn-primary" type="submit">提交</button>';
\yii\widgets\ActiveForm::end();
