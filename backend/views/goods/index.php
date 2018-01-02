<form id="" class="form-inline" action="" method="get" role="form">
    <div class="form-group">
        <input type="text" class="form-control" name="name" placeholder="商品名称">
    </div>
    <div class="form-group ">
        <input type="text" class="form-control" name="sn" placeholder="货号" aria-invalid="false">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="price_max" placeholder="￥">
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name="price_min" placeholder="￥">
    </div>
    <button type="submit" class="btn btn-info glyphicon glyphicon-search">搜索</button>
</form>

<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">商品名</th>
        <th style="text-align: center">货号</th>
        <th style="text-align: center">LOGO图片</th>
        <th style="text-align: center">商品分类</th>
        <th style="text-align: center">品牌分类</th>
        <th style="text-align: center">市场价格</th>
        <th style="text-align: center">商品价格</th>
        <th style="text-align: center">库存</th>
        <th style="text-align: center">是否在销</th>
        <th style="text-align: center">状态</th>
        <th style="text-align: center">排序</th>
        <th style="text-align: center">添加时间</th>
        <th style="text-align: center">浏览次数</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= $row->name ?></td>
            <td><?= $row->sn ?></td>
            <td><img src="<?= $row->logo ?>" width="50px"></td>
            <td><?= $category[$row->goods_category_id] ?></td>
            <td><?= $value[$row->brand_id] ?></td>
            <td><?= $row->market_price ?></td>
            <td><?= $row->shop_price ?></td>
            <td><?= $row->stock ?></td>
            <td>
                <?= $row->is_on_sale == 0 ? '下架' : '' ?>
                <?= $row->is_on_sale == 1 ? '在销' : '' ?>
            </td>
            <td>
                <?= $row->status == 0 ? '回收站' : '' ?>
                <?= $row->status == 1 ? '正常' : '' ?>
            </td>
            <td><?= $row->sort ?></td>
            <td><?= date('Y-m-d H:i:s', $row->create_time) ?></td>
            <td><?= $row->view_times ?></td>
            <td>
                <?= \yii\helpers\Html::a('修改', ['goods/edit', 'id' => $row->id], ['class' => 'btn btn-warning']) ?>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger', 'id' => $row->id]) ?><br/>
                <?= \yii\helpers\Html::a('相册', ['goods/gallery', 'id' => $row->id], ['class' => 'btn btn-info glyphicon glyphicon-picture']) ?>
                <?= \yii\helpers\Html::a('预览', ['goods/preview', 'id' => $row->id], ['class' => 'btn btn-success glyphicon glyphicon-eye-open']) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="15" style="text-align: center">
            <?= \yii\helpers\Html::a('添加', ['goods/add'], ['class' => 'btn btn-info']) ?>
        </td>
    </tr>
</table>
<?php
/**
 * @var $this yii\web\View
 */
$url = \yii\helpers\Url::to(['goods/delete']);
$js = <<<JS
    $('tr').on('click','.btn-danger',function() {
      var id = $(this).attr('id');
      var result = confirm('是否删除');
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

<!--分页显示-->
<?= \yii\widgets\LinkPager::widget([
    'pagination' => $pager,
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
]) ?>


