<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">ID</th>
        <th style="text-align: center">品牌名称</th>
        <th style="text-align: center">品牌简介</th>
        <th style="text-align: center">品牌图片</th>
        <th style="text-align: center">排序</th>
        <th style="text-align: center">状态</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($brand as $brands):?>
        <tr>
            <td><?=$brands->id?></td>
            <td><?=$brands->name?></td>
            <td><?=$brands->intro?></td>
            <td><img width="100px" src="<?= $brands->logo ?>" class="img-circle"></td>
            <td><?=$brands->sort?></td>
            <td>
                <?=$brands->status==1?'正常':''?>
                <?=$brands->status==0?'隐藏':''?>
                <?=$brands->status==-1?'删除':''?>
            </td>
            <td>
                <a href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$brands->id])?>" class="btn btn-warning glyphicon glyphicon-cog">修改</a>
                <?=\yii\helpers\Html::button('删除',['class'=>'btn btn-danger glyphicon glyphicon-trash','id'=>$brands->id])?>
            </td
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="7"><a href="<?=\yii\helpers\Url::to(['brand/add'])?>" class=" btn btn-info">添加</a></td>
    </tr>
</table>
<?php
$url = \yii\helpers\Url::to(['brand/delete']);
$js =
    <<<JS
        $('.btn').click(function() {
        var id = $(this).attr('id');
        $(this).closest('tr').remove();
        $.getJSON('$url?id='+id,function(data) {
        })
   });
JS;
$this->registerJs($js);
?>


<?=\yii\widgets\LinkPager::widget([
    'pagination' => $pager,
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
])?>

