<?php

namespace albertborsos\ddd\rest\cache;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Class Action
 * @package mito\cms\core\rest\admin
 *
 * @property \mito\cms\core\rest\Controller $controller
 * @since 1.1.0
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
    public function findEntity($id): ?EntityInterface
    {
        if ($this->findEntity !== null) {
            return call_user_func($this->findEntity, $id, $this);
        }

        $entity = $this->getRepository()->findById($id);

        if (isset($entity)) {
            return $entity;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }

    /**
     * @return CacheRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository(): ?CacheRepositoryInterface
    {
        return \Yii::createObject($this->repositoryInterface);
    }
}
