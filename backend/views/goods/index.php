<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">ID</th>
        <th style="text-align: center">商品名称</th>
        <th style="text-align: center">货号</th>
        <th style="text-align: center">LOGO图片</th>
        <th style="text-align: center">商品分类id</th>
        <th style="text-align: center">品牌分类</th>
        <th style="text-align: center">市场价格</th>
        <th style="text-align: center">商品价格</th>
        <th style="text-align: center">库存</th>
        <th style="text-align: center">是否在售</th>
        <th style="text-align: center">状态</th>
        <th style="text-align: center">排序</th>
        <th style="text-align: center">添加时间</th>
        <th style="text-align: center">浏览次数</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($goods as $good): ?>
        <tr>
            <td><?= $good->id ?></td>
            <td><?= $good->name ?></td>
            <td><?= $good->sn ?></td>
            <td><?= $good->logo ?></td>
            <td><?= $good->goods_category_id ?></td>
            <td><?= $good->brand_id ?></td>
            <td><?= $good->market_price ?></td>
            <td><?= $good->shop_price ?></td>
            <td><?= $good->stock ?></td>
            <td><?= $good->is_on_sale ?></td>
            <td><?= $good->status ?></td>
            <td><?= $good->sort ?></td>
            <td><?= $good->create_time ?></td>
            <td><?= $good->view_times ?></td>
            <td>
                <a href="" class="btn btn-warning">修改</a>
                <a href="" class="btn btn-danger">删除</a>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="15"><a href="" class="btn btn-info">添加</a></td>
    </tr>
</table>
<?= \yii\widgets\LinkPager::widget([
    'pagination' => $pager,
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',
]) ?>