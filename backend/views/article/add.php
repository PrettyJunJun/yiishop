<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'intro')->textInput();
echo $form->field($model,'article_category_id')->dropDownList($options);
echo $form->field($model, 'sort')->textInput();
echo $form->field($model, 'status', ['inline' => 1])->radioList([0 => '隐藏', 1 => '正常']);
echo $form->field($model, 'content')->widget(\common\widgets\ueditor\Ueditor::className(),[
    'options'=>[
        'initialFrameWidth' => 1000,
    ]
]);
echo '<button type="submit" class="btn btn-info">提交</button>';
\yii\bootstrap\ActiveForm::end();