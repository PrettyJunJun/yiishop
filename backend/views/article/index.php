<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">ID</th>
        <th style="text-align: center">文章名称</th>
        <th style="text-align: center">文章简介</th>
        <th style="text-align: center">文章分类</th>
        <th style="text-align: center">文章排序</th>
        <th style="text-align: center">文章状态</th>
        <th style="text-align: center">创建时间</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($article as $articles): ?>
        <tr>
            <td><?= $articles->id ?></td>
            <td><?= $articles->name ?></td>
            <td><?= $articles->intro ?></td>
            <td><?= $arr[$articles->article_category_id] ?></td>
            <td><?= $articles->sort ?></td>
            <td>
                <?= $articles->status == 1 ? '正常' : '' ?>
                <?= $articles->status == 0 ? '隐藏' : '' ?>
                <?= $articles->status == -1 ? '删除' : '' ?>
            </td>
            <td><?= date('Y-m-d H:i:s', $articles->create_time) ?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['article/edit', 'id' => $articles->id]) ?>" class="btn btn-warning glyphicon glyphicon-cog">修改</a>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger glyphicon glyphicon-trash', 'id' => $articles->id]) ?>
            </td>

        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="8"><a href="<?= \yii\helpers\Url::to(['article/add']) ?>" class=" btn btn-info">添加</a></td>
    </tr>
</table>
<?php
$url = \yii\helpers\Url::to(['article/delete']);
$js =
    <<<JS
         $('tr').on('click','.btn-danger',function() {
      var id = $(this).attr('id');
      var result = confirm('您真的要删除吗');
      if(result){
          $(this).closest('tr').remove();
          $.getJSON("$url?id="+id,function(data) {
        if(data){
        }else{
            alert('删出失败')
}      })
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

