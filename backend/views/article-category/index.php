<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">ID</th>
        <th style="text-align: center">文章名称</th>
        <th style="text-align: center">文章简介</th>
        <th style="text-align: center">排序</th>
        <th style="text-align: center">状态</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($article_category as $articles): ?>
        <tr id="<?= $articles->id ?>">
            <td><?= $articles->id ?></td>
            <td><?= $articles->name ?></td>
            <td><?= $articles->intro ?></td>
            <td><?= $articles->sort ?></td>
            <td><?= $articles->status == 1 ? '正常' : '' ?><?= $articles->status == 0 ? '隐藏' : '' ?><?= $articles->status == -1 ? '删除' : '' ?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['article-category/edit', 'id' => $articles->id]) ?>"
                   class="btn btn-warning">修改</a>
                <a href="<?= \yii\helpers\Url::to(['article-category/delete', 'id' => $articles->id]) ?>?"
                   class="btn btn-danger">删除</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="7"><a href="<?= \yii\helpers\Url::to(['article-category/add']) ?>" class=" btn btn-info">添加</a>
        </td>
    </tr>
</table>
<?php
$url = \yii\helpers\Url::to(['article-category/delete']);
$js =
    <<<JS
        $('tr').on('click','.btn-warning',function() {
var id = $('.btn-warning').closest('tr').attr('id');
        $(this).closest('tr').remove();
        if( confirm('是否删除')){
                $.getJSON('$url?id='+id,function(data) {
        if(data){
        alert('删除成功');
    }else{
        alert('删除失败');
        }
    })
        }
   });
JS;
$this->registerJs($js);
?>


<?= \yii\widgets\LinkPager::widget([
    'pagination' => $pager,
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
]) ?>

