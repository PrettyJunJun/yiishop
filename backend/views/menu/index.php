<table class="table" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">菜单名称</th>
        <th style="text-align: center">地址/路由</th>
        <th style="text-align: center">排序</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($model as $rows):?>
        <tr>
            <td><?=$rows->name?></td>
            <td><?=$rows->url?></td>
            <td><?=$rows->sort?></td>
            <td>
                <?= \yii\helpers\Html::a('修改', ['menu/edit', 'id' => $rows->id], ['class' => 'btn btn-warning glyphicon glyphicon-cog']) ?>
                <?= \yii\helpers\Html::a('删除', ['menu/delete', 'id' => $rows->id], ['class' => 'btn btn-danger glyphicon glyphicon-trash']) ?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="6">
            <?= \yii\helpers\Html::a('添加', ['menu/add'], ['class' => 'btn btn-info']) ?>
        </td>
    </tr>
</table>