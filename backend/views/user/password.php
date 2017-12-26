<?php
$form = \yii\widgets\ActiveForm::begin();
echo $form->field($model, 'username')->textInput();
echo $form->field($model, 'newpassword')->passwordInput();
echo $form->field($model, 'password_hash')->passwordInput();
echo $form->field($model, 'confirm')->passwordInput();
echo $form->field($model, 'email')->textInput();
echo $form->field($model, 'status')->radioList(['0' => '禁用', '1' => '可用']);
echo \yii\helpers\Html::submitButton('提交', ['class' => 'btn btn-info']);
\yii\widgets\ActiveForm::end();