<?php

namespace albertborsos\ddd\web;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Class AbstractActiveController
 * @package albertborsos\ddd\web
 */
abstract class AbstractActiveController extends \yii\web\Controller
{
    /** @var ActiveRepositoryInterface */
    protected $repository;

    public function init()
    {
        parent::init();
        $this->repository = \Yii::createObject($this->repository);
    }

    /**
     * Finds the entity based on its primary key value.
     * If the entity is not found, a 404 HTTP exception will be thrown.
     * @param $id
     * @return EntityInterface|null
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    protected function findEntity($id): ?EntityInterface
    {
        $entity = $this->getRepository()->findOne($id);
        if ($entity === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $entity;
    }

    /**
     * @param string|null $interface
     * @return ActiveRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository($interface = null): ActiveRepositoryInterface
    {
        if (empty($interface)) {
            return $this->repository;
        }

        return \Yii::createObject($interface);
    }
}
