<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'parent_id')->hiddenInput();

//=======================ztree===================//
//>>加载ztree需要的css和js
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js', [
    //>.指定该js文件依赖于jquery (在jquery文件之后加载)
    'depends' => \yii\web\JqueryAsset::className()
]);
$nodes = \backend\models\GoodsCategory::getNodes();

if ($model->id) {
    $id = $model->id;
} else {
    $id = 0;
}

$js = <<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode) {
		          //>>节点被点击 获取改节点id 赋值给("#goodscategory-parent_id")
		          $("#goodscategory-parent_id").val(treeNode.id);
		          
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
       
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //>>展开所有节点
            zTreeObj.expandAll(true);
            //>>节点选中状态回显
            var node = zTreeObj.getNodeByParam("id",$id,null);
            zTreeObj.selectNode(node);
      
JS;
$this->registerJs($js);

echo <<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;
//=======================ztree===================//

echo $form->field($model, 'intro')->textInput();


echo '<button type="submit" class="btn btn-info">提交</button>';
\yii\bootstrap\ActiveForm::end();

