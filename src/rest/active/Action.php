<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\di\Helper;
use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

/**
 * Class Action
 * @package mito\cms\core\rest\admin
 *
 * @property \mito\cms\core\rest\Controller $controller
 */
class Action extends \albertborsos\ddd\rest\Action
{
    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return EntityInterface the model found
     * @throws NotFoundHttpException if the model cannot be found
     * @throws InvalidConfigException
     */
    public function findModel($id): ?EntityInterface
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        $model = $this->getRepository()->findOne($this->getPrimaryKeyCondition($id));

        if (isset($model)) {
            return $model;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }

    /**
     * @return ActiveRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository(): ActiveRepositoryInterface
    {
        return \Yii::createObject($this->repositoryInterface);
    }

    protected function getPrimaryKeyCondition($id)
    {
        $repository = $this->getRepository();
        /** @var ActiveRecordInterface $modelClass */
        $modelClass = Helper::createObject([$this->repositoryInterface, 'dataModelClass']);
        $keys = $modelClass::primaryKey();
        if (count($keys) > 1) {
            $values = explode(',', $id);
            if (count($keys) === count($values)) {
                return array_combine($keys, $values);
            }
        } elseif ($id !== null) {
            return $id;
        }

        return false;
    }
}
