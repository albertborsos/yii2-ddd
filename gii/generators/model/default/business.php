<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->getBusinessClass(true) ?>;

use Yii;
use app\models\BusinessObject;
use <?= $generator->ns .'\\Abstract' . $generator->modelClass ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getBusinessClass()) ?> extends Abstract<?= $generator->modelClass ?> implements BusinessObject<?= "\n" ?>
{

}
