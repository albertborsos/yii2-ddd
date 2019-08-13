<?php

namespace albertborsos\ddd\web;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Class AbstractCacheController
 * @package albertborsos\ddd\web
 */
abstract class AbstractCacheController extends \yii\web\Controller
{
    /** @var CacheRepositoryInterface */
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
        $entity = $this->getRepository()->findById($id);
        if ($entity === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $entity;
    }

    /**
     * @param string|null $interface
     * @return CacheRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository($interface = null): CacheRepositoryInterface
    {
        if (empty($interface)) {
            return $this->repository;
        }

        return \Yii::createObject($interface);
    }
}
