<?php

namespace albertborsos\ddd\web;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

abstract class Controller extends \yii\web\Controller
{
    /** @var RepositoryInterface */
    protected $repository;

    public function init()
    {
        parent::init();
        $this->repository = \Yii::createObject($this->repository);
    }

    /**
     * @param string|null $interface
     * @return ActiveRepositoryInterface
     * @throws InvalidConfigException
     */
    public function getRepository($interface = null): RepositoryInterface
    {
        if (empty($interface)) {
            return $this->repository;
        }

        return \Yii::createObject($interface);
    }

    /**
     * Finds the entity based on its primary key value.
     * If the entity is not found, a 404 HTTP exception will be thrown.
     * @param $id
     * @return EntityInterface|null
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    abstract protected function findEntity(): ?EntityInterface;
}
