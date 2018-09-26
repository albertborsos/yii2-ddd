<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>


/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

<?= $generator->enablePjax ? "    <?php Pjax::begin([
        'id' => '" . Inflector::camel2id(StringHelper::basename($generator->modelClass)) . "-index-list',
        'clientOptions' => [
            'type' => 'POST',
        ],
    ]); ?>" : '' ?>

    <?= "<?php " ?>\albertborsos\themehelper\inspinia\ThemeHelper::setMiddleBarContent($this->render('_search', [
        'model' => $searchModel,
        'pjaxContainerSelector' => '#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index-list',
    ])) ?>
    <div class="ibox">
        <div class="ibox-content">
<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        $format = $generator->generateColumnFormat($column);
        switch ($column->name) {
            case 'id':
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
            [
                'class' => 'yii\grid\ActionColumn',
                'buttonOptions' => [
                    'class' => 'btn btn-default btn-xs',
                ],
                'template' => '{view} {update} {status} {delete}',
                'visibleButtons' => [
                    'view' => Yii::$app->user->can('view<?= StringHelper::basename($generator->modelClass) ?>'),
                    'update' => function ($model) {
                        return Yii::$app->user->can('update<?= StringHelper::basename($generator->modelClass) ?>', ['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>Id' => $model->id]);
                    },
                    'delete' => function ($model) {
                        return Yii::$app->user->can('delete<?= StringHelper::basename($generator->modelClass) ?>', ['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>Id' => $model->id]);
                    },
<?php if(in_array('status', $generator->getColumnNames())): ?>
                    'status' => function ($model) {
                        return Yii::$app->user->can('update<?= StringHelper::basename($generator->modelClass) ?>', ['<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>Id' => $model->id]);
                    },
<?php endif; ?>
                ],
<?php if(in_array('status', $generator->getColumnNames())): ?>
                'buttons' => [
                    'status' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-refresh"></span>', $url, [
                            'title' => Yii::t('yii', 'Toggle Status'),
                            'data-pjax' => '0',
                            'data-method' => 'post',
                            'class' => 'btn btn-xs btn-default',
                        ]);
                    },
                ],
<?php endif; ?>
            ],
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]) ?>
<?php endif; ?>
<?= $generator->enablePjax ? '<?php Pjax::end(); ?>' : '' ?>

        </div>
    </div>
</div>
