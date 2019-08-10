<?php

namespace albertborsos\ddd\traits;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use yii\base\InvalidConfigException;

/**
 * Trait ActiveFormTrait
 * @package albertborsos\ddd\traits
 * @property $dataModelClass
 * @since 2.0.0
 */
trait ActiveFormTrait
{
    public function init()
    {
        parent::init();
        $this->repository = \Yii::createObject($this->repository);
        $this->validateRepository($this->repository);
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

        $repository = \Yii::createObject($interface);
        $this->validateRepository($repository);

        return $repository;
    }

    protected function validateRepository($repository): void
    {
        if (!$repository instanceof ActiveRepositoryInterface) {
            throw new InvalidConfigException(get_class($repository) . '::$repository must implements `' . ActiveRepositoryInterface::class . '`');
        }
    }
}
