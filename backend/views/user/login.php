<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'username')->textInput();
echo $form->field($model, 'password_hash')->passwordInput();

echo $form->field($model,'remember')->checkbox(['1'=>'记住密码']);
//验证码
echo $form->field($model, 'code')->widget(\yii\captcha\Captcha::className(), [
    'captchaAction' => 'user/captcha',
    'template' => '<div class="row"><div class="col-xs-1">{input}</div><div class="col-xs-1">{image}</div></div>'
]);

echo \yii\bootstrap\Html::submitButton('登录', ['class' => 'btn btn-info']);
\yii\bootstrap\ActiveForm::end();