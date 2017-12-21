<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'intro')->textInput();
//echo $form->field($model,'imgFile')->fileInput()->label("品牌LOGO");
echo $form->field($model,'logo')->hiddenInput();
//===============web uploader=================//
//注册插件css和js文件
$this->registerCssFile('@web/webuploader/webuploader.css');
$this->registerJsFile('@web/webuploader/webuploader.js', [
    //>.指定该js文件依赖于jquery (在jquery文件之后加载)
    'depends' => \yii\web\JqueryAsset::className()
]);
echo
<<<HTML
<!--dom结构部分-->
<div id="uploader-demo">
    <!--用来存放item-->
    <img id="img" src="$model->logo" width="100px"/>
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
HTML;
$upload_url = \yii\helpers\Url::to(['brand/upload']);
$js =
    <<<JS
// 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    swf: '/webuploader/Uploader.swf',

    // 文件接收服务端。
    server: '{$upload_url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/*'
    }
});

// 文件上传成功，给item添加成功class, 用样式标记上传成功。
uploader.on( 'uploadSuccess', function( file , response) {
    //>>上传成功后显示图片地址
    // response.url;
    $("#img").attr('src',response.url);
    //>>将上传成功的图片上传logo字段
    $("#brand-logo").val(response.url);
});
JS;
$this->registerJs($js);


//===============web uploader=================//
echo $form->field($model, 'sort')->textInput();
echo $form->field($model, 'status', ['inline' => 1])->radioList([0 => '隐藏', 1 => '正常']);
echo '<button type="submit" class="btn btn-info">提交</button>';
\yii\bootstrap\ActiveForm::end();