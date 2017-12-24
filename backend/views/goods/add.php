<?php

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sn')->textInput();
echo $form->field($model,'logo')->textInput();
echo $form->field($model,'goods_category_id')->textInput();
echo $form->field($model,'brand_id')->textInput();
echo $form->field($model,'market_price')->textInput();
echo $form->field($model,'shop_price')->textInput();
\yii\bootstrap\ActiveForm::end();