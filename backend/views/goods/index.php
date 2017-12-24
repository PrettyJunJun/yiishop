<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">ID</th>
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
    <?php foreach ($form as $r): ?>
        <tr>
            <td><?= $r->id ?></td>
            <td><?= $r->name ?></td>
            <td><?= $r->sn ?></td>
            <td><img src="<?= $r->logo ?>" width="50px"></td>
            <td><?= $f[$r->goods_category_id] ?></td>
            <td><?= $v[$r->brand_id] ?></td>
            <td><?= $r->market_price ?></td>
            <td><?= $r->shop_price ?></td>
            <td><?= $r->stock ?></td>
            <td>
                <?= $r->is_on_sale == 0 ? '下架' : '' ?>
                <?= $r->is_on_sale == 1 ? '在销' : '' ?>
            </td>
            <td>
                <?= $r->status == 0 ? '回收站' : '' ?>
                <?= $r->status == 1 ? '正常' : '' ?>
            </td>
            <td><?= $r->sort ?></td>
            <td><?= date('Y-m-d H:i:s', $r->create_time) ?></td>
            <td><?= $r->view_times ?></td>
            <td>
                <?= \yii\helpers\Html::a('相册', ['goods/gallery', 'id' => $r->id], ['class' => 'btn btn-info']) ?>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger', 'id' => $r->id]) ?>
                <?= \yii\helpers\Html::a('修改', ['goods/edit', 'id' => $r->id], ['class' => 'btn btn-warning']) ?>

                <?= \yii\helpers\Html::a('预览', ['goods/preview', 'id' => $r->id], ['class' => 'btn btn-info']) ?>
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
