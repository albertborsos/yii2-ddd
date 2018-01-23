<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getAbstractFormClass(true) ?>;

use \albertborsos\ddd\interfaces\FormObject;
use <?= ltrim($generator->modelClass) ?>;
use yii\base\Model;

class <?= \yii\helpers\StringHelper::basename($generator->getAbstractFormClass()) ?> extends Model implements FormObject
{
    public $id;

    public function attributeLabels()
    {
        return (new <?= \yii\helpers\StringHelper::basename($generator->modelClass) ?>())->attributeLabels();
    }
}
