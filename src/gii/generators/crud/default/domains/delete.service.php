<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\crud\Generator */

echo "<?php\n";
?>

namespace <?= $generator->getDeleteServiceClass(true) ?>;

use \albertborsos\ddd\models\AbstractService;

class <?= \yii\helpers\StringHelper::basename($generator->getDeleteServiceClass()) ?> extends AbstractService
{
    /**
     * Business logic to delete data
     *
     * @return mixed
     */
    public function execute()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // if ($imageModel = $this->getModel()->image) {
            //     $service = new DeleteImageService($imageModel);
            //     if (!$service->execute()) {
            //        throw new Exception('Image cannot be removed!');
            //     }
            // }

            if (!$this->getModel()->delete()) {
                throw new Exception('<?= \yii\helpers\StringHelper::basename($generator->modelClass) ?> cannot be removed!');
            }
            $transaction->commit();
            $this->setId($this->getModel()->getId());
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        return false;
    }
}
