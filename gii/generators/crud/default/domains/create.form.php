<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getCreateFormClass(true) ?>;

use <?= ltrim($generator->getAbstractFormClass()) ?>;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getCreateFormClass()) ?> extends <?= \yii\helpers\StringHelper::basename($generator->getAbstractFormClass()) . "\n" ?>
{
    // public $email;

    public function rules()
    {
        return [
            // [['email'], 'unique', 'targetClass' => <?= \yii\helpers\StringHelper::basename($generator->modelClass) ?>::className(), 'targetAttribute' => 'email'],
        ];
    }
}
