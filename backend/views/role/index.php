<table class="table table-bordered" style="text-align: center" id="table_id_example" class="display">
    <tr style="color: #7a43b6">
        <th style="text-align: center">角色名称</th>
        <th style="text-align: center">描述</th>
        <th style="text-align: center">操作</th>
    </tr>
    <?php foreach ($authManager as $authManagers):?>
        <tr>
            <td><?=$authManagers->name?></td>
            <td><?=$authManagers->description?></td>
            <td>
                <?= \yii\helpers\Html::a('修改', ['role/edit', 'name' => $authManagers->name], ['class' => 'btn btn-warning glyphicon glyphicon-cog']) ?>
                <?= \yii\helpers\Html::button('删除', ['class' => 'btn btn-danger glyphicon glyphicon-trash', 'name' => $authManagers->name]) ?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="3">
            <?= \yii\helpers\Html::a('添加', ['role/add'], ['class' => 'btn btn-info']) ?>
        </td>
    </tr>
</table>
<?php
/**
 * @var $this yii\web\View
 */
$url = \yii\helpers\Url::to(['role/delete']);
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

