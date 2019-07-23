<?php

namespace albertborsos\ddd\traits;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use yii\base\InvalidConfigException;

/**
 * Trait ActiveFormTrait
 * @package albertborsos\ddd\traits
 * @property $dataModelClass
 * @since 1.1.0
 */
trait ActiveFormTrait
{
    public function init()
    {
        parent::init();
        $this->repository = \Yii::createObject($this->repository);
        if (!$this->repository instanceof ActiveRepositoryInterface) {
            throw new InvalidConfigException(get_class($this) . '::$repository must implements ' . ActiveRepositoryInterface::class);
        }
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

        if (!$repository instanceof ActiveRepositoryInterface) {
            throw new InvalidConfigException(get_class($this) . '::$repository must implements ' . ActiveRepositoryInterface::class);
        }

        return $repository;
    }
}
