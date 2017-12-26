<?php
echo \yii\helpers\Html::a('返回', ['goods/index'], ['class' => 'btn btn-primary glyphicon glyphicon-arrow-left']);
/**
 * @var $this \yii\web\View
 */
$this->registerJsFile('@web/webuploader/webuploader.js', ['depends' => \yii\web\YiiAsset::className()]);
$this->registerCssFile('@web/webuploader/webuploader.css');
echo <<<html
    <div id="uploader-demo">
    <div id="fileList" class="uploader-list"></div>
    <div id="filePicker">选择图片</div>
</div>
html;
$url = \yii\helpers\Url::to(['goods/uploader']);
$u = \yii\helpers\Url::to(['goods/gallery-add']);
$de = \yii\helpers\Url::to(['goods/gallery-delete']);
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
   
    //用post提交数据并获得新添加那条数据的id
    $.post('{$u}',{'id':$id,'resu':response.url},function(data) {
        if(data){
                var html="<tr><td><img src='"+response.url+"'></td><td><button class='btn btn-primary' id='"+data.id+"'>删除</button></td></tr>";
    $('table').append(html)
        }else{
            alert('添加失败');
        }
    },'json');

});
JS;
$this->registerJs($js);
?>
<table class="table">
    <tr style="color: #7a43b6">
        <th>图片</th>
        <th>操作</th>
    </tr>
    <?php foreach ($model as $img): ?>
        <tr>
            <td>
                <img src="<?= $img->path ?>">
            </td>
            <td>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger', 'id' => $img->id]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<!--删除-->
<?php
/**
 * @var $this yii\web\View
 */
$url = \yii\helpers\Url::to(['goods/gallerydelete']);
$js_1 = <<<JS
    $('tr').on('click','.btn-primary',function() {
      var id = $(this).attr('id');
      console.debug(id);
      var result = confirm('是否删除');
      if(result){
         var n=$(this);
          $.getJSON("$de?id="+id,function(data) {
        if(data){
            n.closest('tr').remove()
        }else{
            alert('删出失败')}
          })
          
      }
    });
JS;
$this->registerJs($js_1);
?>
