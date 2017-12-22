<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">ID</th>
        <th style="text-align: center">商品名称</th>
        <th style="text-align: center">上级分类ID</th>
        <th style="text-align: center">简介</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($goods_category as $category): ?>
        <tr>
            <td><?= $category->id ?></td>
            <td><?= $category->name ?></td>
            <td><?= $category->parent_id ?></td>
            <td><?= $category->intro ?></td>
            <td>
                <a href="<?= \yii\helpers\Url::to(['goods-category/edit', 'id' => $category->id]) ?>"
                   class="btn btn-warning">修改</a>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger', 'id' => $category->id]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="5"><a href="<?= \yii\helpers\Url::to(['goods-category/add']) ?>" class=" btn btn-info">添加</a></td>
    </tr>
</table>
<?php
$url = \yii\helpers\Url::to(['goods-category/delete']);
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

