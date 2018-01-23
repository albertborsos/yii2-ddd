<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getUpdateDomainClass(true) ?>;

use \albertborsos\ddd\models\AbstractDomain;
use <?= ltrim($generator->getResourceClass()) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getUpdateDomainClass()) ?> extends AbstractDomain
{
    /**
     * Business logic to store data for multiple resources.
     *
     * @return mixed
     */
    public function process()
    {
        $resource = new <?= \yii\helpers\StringHelper::basename($generator->getResourceClass()) ?>($this->getForm(), $this->getModel());
        if ($resource->save()) {
            $this->setId($resource->getId());
            return true;
        }

        $this->addErrors($resource->getErrors());
        return false;
    }
}
