<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);
?>

namespace <?= $generator->getToggleStatusServiceClass(true) ?>;

use \albertborsos\ddd\models\AbstractService;
use <?= ltrim($generator->getToggleStatusFormClass()) ?>;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getToggleStatusServiceClass()) ?> extends AbstractService
{
    /**
     * @return bool
     */
    public function execute()
    {
        $form = $this->toggleStatus($this->getForm());

        $model = $this->getModel();
        $model->load($form->attributes, '');

        if ($model->save()) {
            $this->setId($model->id);
            return true;
        }

        $this->getForm()->addErrors($model->getErrors());

        return false;
    }

    /**
     * @return <?= \yii\helpers\StringHelper::basename($generator->getToggleStatusFormClass()) . "\n" ?>
     */
    private function toggleStatus(<?= \yii\helpers\StringHelper::basename($generator->getToggleStatusFormClass()) ?> $form)
    {
        $form = clone $form;
        switch ($form->status) {
            case <?= $modelClassBaseName ?>::STATUS_ACTIVE:
                $form->status = <?= $modelClassBaseName ?>::STATUS_INACTIVE;
                break;
            case <?= $modelClassBaseName ?>::STATUS_INACTIVE:
                $form->status = <?= $modelClassBaseName ?>::STATUS_ACTIVE;
                break;
        }

        return $form;
    }
}
