<?php
$this->registerCssFile('@web/DataTables/media/css/jquery.dataTables.css');
$this->registerJsFile('@web/DataTables/media/js/jquery.dataTables.js', [
    'depends' => \yii\web\JqueryAsset::className()
]);
$js = <<<JS
$(document).ready( function () {
    $('#table_id_example').DataTable({
    language: {
       "sProcessing": "处理中...",
       "sLengthMenu": "显示 _MENU_ 项结果",
       "sZeroRecords": "没有匹配结果",
       "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
       "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
       "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
       "sInfoPostFix": "",
       "sSearch": "搜索:",
       "sUrl": "",
       "sEmptyTable": "表中数据为空",
       "sLoadingRecords": "载入中...",
       "sInfoThousands": ",",
       "oPaginate": {
           "sFirst": "首页",
           "sPrevious": "上页",
           "sNext": "下页",
           "sLast": "末页"
       },
       "oAria": {
           "sSortAscending": ": 以升序排列此列",
           "sSortDescending": ": 以降序排列此列"
       }
    }
    });
});
JS;
$this->registerJs($js);
?>
<table id="table_id_example" class="table table-bordered display" style="text-align: center">
    <thead>
    <tr style="color: #7a43b6">
        <th style="text-align: center">名称</th>
        <th style="text-align: center">描述</th>
        <th style="text-align: center">操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($authManager as $authManagers): ?>
        <tr>
            <td><?= $authManagers->name ?></td>
            <td><?= $authManagers->description ?></td>
            <td>
                <?= \yii\helpers\Html::a('修改', ['rbac/edit', 'name' => $authManagers->name], ['class' => 'btn btn-warning glyphicon glyphicon-cog']) ?>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger glyphicon glyphicon-trash', 'name' => $authManagers->name]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tr>
        <td colspan="3">
            <?= \yii\helpers\Html::a('添加', ['rbac/add-permission'], ['class' => 'btn btn-info']) ?>
        </td>

    </tr>
</table>
<?php
/**
 * @var $this yii\web\View
 */
$url = \yii\helpers\Url::to(['rbac/delete']);
$js = <<<JS
    $('tr').on('click','.btn-danger',function() {
      var name = $(this).attr('name');
      var result = confirm('是否删除');
      if(result){
          $(this).closest('tr').remove();
          $.getJSON("$url?name="+name,function(data) {
        if(data){
        }else{
            alert('删出失败')
}      })
      }
    });
JS;
$this->registerJs($js);
?>

