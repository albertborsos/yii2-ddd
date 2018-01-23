<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getUpdateFormClass(true) ?>;

use <?= ltrim($generator->getAbstractFormClass()) ?>;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getUpdateFormClass()) ?> extends <?= \yii\helpers\StringHelper::basename($generator->getAbstractFormClass()) . "\n" ?>
{
    // public $email;

    public function __construct(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model, array $config = [])
    {
        $this->preloadAttributes($model);
        parent::__construct($config);
    }

    private function preloadAttributes(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model)
    {
        $this->id = $model->id;
        // $this->email = $model->email;
    }

    public function rules()
    {
        return [
            // [['email'], 'unique', 'targetClass' => <?= \yii\helpers\StringHelper::basename($generator->modelClass) ?>::className(), 'targetAttribute' => 'email', 'filter' => function ($query) {
            //     /** @var \yii\db\Query $query */
            //     return $query->andWhere(['NOT IN', 'id', $this->id]);
            // }],
        ];
    }
}
