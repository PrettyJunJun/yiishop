<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'username')->textInput();
echo $form->field($model, 'password_hash')->passwordInput();
echo $form->field($model, 'email')->textInput();
echo $form->field($model, 'status', ['inline' => 1])->radioList([0 => '禁用', 1 => '启用']);
echo $form->field($model, 'roles', ['inline' => 1])->checkboxList($authManagers);
echo '<button type="submit" class="btn btn-info">提交</button>';
\yii\bootstrap\ActiveForm::end();