<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/DataTables/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/DataTables/media/js/jquery.dataTables.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
$js=<<<JS
$(document).ready( function () {
    $('#table_id_example').DataTable();
} );
JS;
$this->registerJs($js);
?>
<table id="table_id_example"  class="table table-bordered display" style="text-align: center" >
    <thead>
    <tr style="color: #7a43b6">
        <th style="text-align: center">名称</th>
        <th style="text-align: center">描述</th>
        <th style="text-align: center">操作</th>
    </tr>
    </thead>
    <?php foreach ($authManager as $authManagers):?>
    <tbody>
        <tr>
            <td><?=$authManagers->name?></td>
            <td><?=$authManagers->description?></td>
            <td>
                <?= \yii\helpers\Html::a('修改', ['rbac/edit', 'name' => $authManagers->name], ['class' => 'btn btn-warning glyphicon glyphicon-cog']) ?>
                <?= \yii\helpers\Html::a('删除', ['rbac/delete', 'name' => $authManagers->name], ['class' => 'btn btn-danger glyphicon glyphicon-trash']) ?>
            </td>
        </tr>
    </tbody>
    <?php endforeach;?>
    <tr>
        <td colspan="3">
            <?= \yii\helpers\Html::a('添加', ['rbac/add-permission'], ['class' => 'btn btn-info']) ?>
        </td>

    </tr>
</table>
