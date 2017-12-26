<table class="table table-bordered" style="text-align: center">
    <tr style="color: #7a43b6">
        <th style="text-align: center">名称</th>
        <th style="text-align: center">描述</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($authManager as $authManagers):?>
        <tr>
            <td><?=$authManagers->name?></td>
            <td><?=$authManagers->description?></td>
            <td>
                <a href="">修改</a>
                <a href="">删除</a>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="3">
            <?= \yii\helpers\Html::a('添加', ['role/add'], ['class' => 'btn btn-info']) ?>
        </td>
    </tr>
</table>