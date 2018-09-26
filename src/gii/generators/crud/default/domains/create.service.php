<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getCreateServiceClass(true) ?>;

use \albertborsos\ddd\models\AbstractService;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getCreateServiceClass()) ?> extends AbstractService
{
    /**
     * @return bool
     */
    public function execute()
    {
        $model = new <?= \yii\helpers\StringHelper::basename($generator->modelClass) ?>();
        $model->load($this->getForm()->attributes, '');

        if ($model->save()) {
            $this->setId($model->getId());
            return true;
        }

        $this->getForm()->addErrors($model->getErrors());

        return false;
    }
}
