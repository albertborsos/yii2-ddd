<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getUpdateServiceClass(true) ?>;

use \albertborsos\ddd\models\AbstractService;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getUpdateServiceClass()) ?> extends AbstractService
{
    /**
     * @return bool
     */
    public function execute()
    {
        $model = $this->getModel();
        $model->load($this->getForm()->attributes, '');

        if ($model->save()) {
            $this->setId($model->id);
            return true;
        }

        $this->getForm()->addErrors($model->getErrors());

        return false;
    }
}
