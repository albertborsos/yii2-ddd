<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getDeleteDomainClass(true) ?>;

use \albertborsos\ddd\models\AbstractDomain;
use <?= ltrim($generator->getResourceClass()) ?>;

class <?= \yii\helpers\StringHelper::basename($generator->getDeleteDomainClass()) ?> extends AbstractDomain
{
    /**
     * Business logic to delete data
     *
     * @return mixed
     */
    public function process()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $resource = new <?= \yii\helpers\StringHelper::basename($generator->getResourceClass()) ?>(null, $this->getModel());
            $resource->delete();

            // if ($imageModel = $this->getModel()->image) {
            //     $imageResource = new ImageResource(null, $imageModel);
            //     if ($imageResource->delete() === false) {
            //         throw new Exception('Image cannot be removed!');
            //     }
            // }

            $this->setId($resource->getId());
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        return false;
    }
}
