<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \albertborsos\ddd\gii\generators\model\Generator */

$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->getResourceClass(), '\\')) ?>;

use Yii;
use \albertborsos\ddd\models\AbstractResource;
use <?= $generator->getBusinessClass() ?>;

/**
 * <?= StringHelper::basename($generator->getResourceClass()) ?> handles the `insert`, `update` and `delete` processes for `<?= $generator->modelClass ?>`.
 */
class <?= StringHelper::basename($generator->getResourceClass()) ?> extends AbstractResource
{
    /**
     * Business logic to store data.
     *
     * @return bool
     */
    protected function insert()
    {
        $model = new <?= $modelClass ?>();
        $model->load($this->getForm()->attributes, '');

        if ($model->save()) {
            $this->setId($model->id);
            return true;
        }

        $this->addErrors($model->getErrors());
        return false;
    }

    /**
     * Business logic to update data.
     *
     * @return bool
     */
    protected function update()
    {
        $model = $this->getModel();
        $model->load($this->getForm()->attributes, '');

        if ($model->save()) {
            return true;
        }

        $this->addErrors($model->getErrors());
        return false;
    }

    /**
     * Business logic to delete data.
     *
     * @return bool
     */
    public function delete()
    {
        $this->getModel()->delete();
        return true;
    }
}
