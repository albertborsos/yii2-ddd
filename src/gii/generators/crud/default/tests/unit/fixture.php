<?php
/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>
return [
    '<?= lcfirst($modelClassBaseName) ?>1' => [
        'id' => 1,
        'name' => '<?= $modelClassBaseName ?> 1',
        'status' => <?= $generator->modelClass ?>::STATUS_ACTIVE,
    ],
    '<?= lcfirst($modelClassBaseName) ?>2' => [
        'id' => 2,
        'name' => '<?= $modelClassBaseName ?> 2',
        'status' => <?= $generator->modelClass ?>::STATUS_INACTIVE,
    ],
    '<?= lcfirst($modelClassBaseName) ?>3' => [
        'id' => 3,
        'name' => '<?= $modelClassBaseName ?> 3',
        'status' => <?= $generator->modelClass ?>::STATUS_DELETED,
    ],
];
