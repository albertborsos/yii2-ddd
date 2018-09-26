<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getUpdateFormClass(true) ?>;

use \yii\base\Model;
use \albertborsos\ddd\interfaces\FormObject;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getUpdateFormClass()) ?> extends Model implements FormObject
{
    // public $email;

    public function __construct(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model, array $config = [])
    {
        $this->preloadAttributes($model);
        parent::__construct($config);
    }

    private function preloadAttributes(<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> $model)
    {
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
