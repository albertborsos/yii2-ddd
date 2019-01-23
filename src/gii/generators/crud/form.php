<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\crud\Generator */

echo $this->render('@vendor/yiisoft/yii2-gii/src/generators/crud/form.php', [
    'form' => $form,
    'generator' => $generator,
]);
echo \yii\helpers\Html::tag('hr');
echo $form->field($generator, 'generateTests')->checkbox();
echo $form->field($generator, 'testPath');
echo \yii\helpers\Html::tag('hr');
