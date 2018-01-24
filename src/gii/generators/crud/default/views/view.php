<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
?>
<?= "<?php " ?> \albertborsos\themehelper\inspinia\ThemeHelper::setMiddleBarContent($this->render('_view-buttons', ['model' => $model])) ?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">
    <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="ibox">
            <div class="ibox-content">

    <?= "<?= " ?>DetailView::widget([
        'model' => $model,
        'attributes' => [
<?php
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        echo "            '" . $name . "',\n";
    }
} else {
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        switch ($column->name) {
            case 'status':
                echo "            'statusText',\n";
                break;
            case 'created_at':
            case 'updated_at':
                echo "            '" . $column->name . ":datetime',\n";
                break;
            default:
                echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                break;
        }
    }
}
?>
        ],
    ]) ?>
            </div>
        </div>
    </div>
</div>

