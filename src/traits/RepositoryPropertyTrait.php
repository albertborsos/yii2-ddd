<?php

namespace albertborsos\ddd\traits;

use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\InvalidConfigException;

trait RepositoryPropertyTrait
{
    /** @var string|RepositoryInterface */
    public $repository;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->initRepository();
    }

    /**
     * @throws InvalidConfigException
     */
    protected function initRepository(): void
    {
        $this->repository = \Yii::createObject($this->repository);
        if (!$this->repository instanceof RepositoryInterface) {
            throw new InvalidConfigException(static::class . '::$repository must implements `' . RepositoryInterface::class . '`');
        }
    }
}
