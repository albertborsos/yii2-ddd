<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $submitButtonLabel string */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => '{label}<div class="col-md-8">{input}</div><div class="col-md-8 col-md-offset-4">{hint}{error}</div>',
            'labelOptions' => ['class' => 'col-md-4 control-label'],
        ],
    ]); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
    }
} ?>
    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <?= "<?= " ?>Html::submitButton($submitButtonLabel, ['class' => 'btn btn-primary']) ?>
            <?= "<?= " ?>Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
