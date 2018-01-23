<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

namespace <?= $generator->getUpdateFormClass(true) ?>;

use <?= ltrim($generator->getAbstractFormClass()) ?>;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getToggleStatusFormClass()) ?> extends <?= \yii\helpers\StringHelper::basename($generator->getAbstractFormClass()) . "\n" ?>
{
    public $status;

    public function __construct(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model, array $config = [])
    {
        $this->preloadAttributes($model);
        parent::__construct($config);
    }

    private function preloadAttributes(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model)
    {
        $this->id = $model->id;
        $this->status = $model->status;
    }

    public function rules()
    {
        return [
            [['status'], 'in', 'range' => [<?= $modelClassBaseName ?>::STATUS_ACTIVE, <?= $modelClassBaseName ?>::STATUS_INACTIVE]],
        ];
    }
}
