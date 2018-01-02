<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->dropDownList($menu_id,['prompt'=>'★请选择上级菜单★']);
echo $form->field($model,'url')->dropDownList($menu,['prompt'=>'★请选择路由★']);
echo $form->field($model,'sort', ['inline' => 1])->radioList([0 => '禁用', 1 => '启用']);

echo '<button type="submit" class="btn btn-info">提交</button>';
\yii\bootstrap\ActiveForm::end();