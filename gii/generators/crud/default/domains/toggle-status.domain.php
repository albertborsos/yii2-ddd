<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\gii\generators\crud\Generator */

echo "<?php\n";

$modelClassBaseName = \yii\helpers\StringHelper::basename($generator->modelClass);
?>

namespace <?= $generator->getToggleStatusDomainClass(true) ?>;

use app\models\AbstractDomain;
use <?= ltrim($generator->getToggleStatusFormClass()) ?>;
use <?= ltrim($generator->getResourceClass()) ?>;
use <?= ltrim($generator->modelClass) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getToggleStatusDomainClass()) ?> extends AbstractDomain
{
    /**
     * Business logic to store data for multiple resources.
     *
     * @return mixed
     */
    public function process()
    {
        $form = $this->toggleStatus();

        $resource = new <?= \yii\helpers\StringHelper::basename($generator->getResourceClass()) ?>($form, $this->getModel());
        if ($resource->save()) {
            $this->setId($resource->getId());
            return true;
        }

        $this->addErrors($resource->getErrors());
        return false;
    }

    /**
     * @return <?= \yii\helpers\StringHelper::basename($generator->getToggleStatusFormClass()) . "\n" ?>
     */
    private function toggleStatus()
    {
        /** @var <?= \yii\helpers\StringHelper::basename($generator->getToggleStatusFormClass()) ?> $form */
        $form = clone $this->getForm();
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
