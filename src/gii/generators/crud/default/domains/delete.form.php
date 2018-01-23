<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getDeleteFormClass(true) ?>;

use <?= ltrim($generator->getAbstractFormClass()) ?>;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getDeleteFormClass()) ?> extends <?= \yii\helpers\StringHelper::basename($generator->getAbstractFormClass()) . "\n" ?>
{
    // public $hasChild;

    public function __construct(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model, array $config = [])
    {
        $this->preloadAttributes($model);
        parent::__construct($config);
    }

    private function preloadAttributes(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model)
    {
        $this->id = $model->id;
        // $this->hasChild = $model->getChildRecords()->exists();
    }

    public function rules()
    {
        return [
            // [['hasChildPage'], 'compare', 'compareValue' => false, 'message' => Yii::t('<?= $generator->messageCategory ?>', 'error.has-child')],
        ];
    }
}
