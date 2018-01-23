<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->searchModelClass, '\\') ?> */
?>
<div class="pull-left">
    <?= '<?= ' ?>Html::a(FA::icon(FA::_CHEVRON_LEFT) . ' ' . Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-default']) ?>
</div>
<div class="pull-right">
    <?= '<?= ' ?>Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= '<?= ' ?>Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
            'method' => 'post',
        ],
    ]) ?>
</div>