<table class="table table-bordered" style="text-align: center">
    <tr>
        <th style="text-align: center">ID</th>
        <th style="text-align: center">用户名</th>
        <th style="text-align: center">邮箱</th>
        <th style="text-align: center">最后登录时间</th>
        <th style="text-align: center">最后登录IP</th>
        <th style="text-align: center">状态</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($model as $row):?>
        <tr>
            <td><?=$row->id?></td>
            <td><?=$row->username?></td>
            <td><?=$row->email?></td>
            <td><?=$row->last_login_time?></td>
            <td><?=$row->last_login_ip?></td>
            <td><?= $row->status == 1 ? '启用' : '' ?><?= $row->status == 0 ? '禁用' : '' ?></td>

            <td>
                <?= \yii\helpers\Html::a('修改', ['user/edit', 'id' => $row->id], ['class' => 'btn btn-warning']) ?>
                <?= \yii\helpers\Html::a('删除', ['user/delete', 'id' => $row->id], ['class' => 'btn btn-danger']) ?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="7" >
            <?= \yii\helpers\Html::a('添加', ['user/add'], ['class' => 'btn btn-info']) ?>
        </td>
    </tr>
</table>
<?= \yii\widgets\LinkPager::widget([
    'pagination' => $pager,
    'nextPageLabel' => '下一页',
    'prevPageLabel' => '上一页',

]) ?>