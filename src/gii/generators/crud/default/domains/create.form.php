<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getCreateFormClass(true) ?>;

use \yii\base\Model;
use \albertborsos\ddd\interfaces\FormObject;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getCreateFormClass()) ?> extends Model implements FormObject
{
    // public $email;

    public function rules()
    {
        return [
            // [['email'], 'unique', 'targetClass' => <?= \yii\helpers\StringHelper::basename($generator->modelClass) ?>::className(), 'targetAttribute' => 'email'],
        ];
    }
}
