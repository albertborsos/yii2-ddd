<?php

namespace albertborsos\ddd\rest;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Trait FormAndServiceActionTrait
 * @package albertborsos\ddd\rest
 * @since 2.0.0
 */
trait FormAndServiceActionTrait
{
    /**
     * Classname of the form model which validates the request.
     * @var string
     */
    public $formClass;

    /**
     * Classname of the service which executes the business logic.
     * @var string
     */
    public $serviceClass;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->formClass)) {
            throw new InvalidConfigException(get_class($this) . '::$formClass must be set.');
        }
        if (empty($this->serviceClass)) {
            throw new InvalidConfigException(get_class($this) . '::$serviceClass must be set.');
        }
    }

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

        /** @var ActiveRepositoryInterface $repository */
        $repository = $this->getRepository();

        $entity = $repository->findOne($this->getPrimaryKeyCondition($id));

        if (isset($entity)) {
            return $entity;
        }

        throw new NotFoundHttpException("Object not found: $id");
    }
}
