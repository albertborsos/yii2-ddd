<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="ibox">
                <div class="ibox-content">
                    <?= "<?= " ?>$this->render('_form', [
                        'model' => $model,
                        'submitButtonLabel' => Yii::t('app', 'Update'),
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
