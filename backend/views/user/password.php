<?php
$form = \yii\widgets\ActiveForm::begin();
echo $form->field($model, 'oldpassword')->passwordInput();
echo $form->field($model, 'newpassword')->passwordInput();
echo $form->field($model, 'confirm')->passwordInput();
echo \yii\helpers\Html::submitButton('提交', ['class' => 'btn btn-info']);
\yii\widgets\ActiveForm::end();