<?php

namespace albertborsos\ddd\traits;

use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\InvalidConfigException;

trait TargetRepositoryPropertyTrait
{
    /** @var string|RepositoryInterface */
    public $targetRepository;

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
        $this->targetRepository = \Yii::createObject($this->targetRepository);
        if (!$this->targetRepository instanceof RepositoryInterface) {
            throw new InvalidConfigException(static::class . '::$repository must implements `' . RepositoryInterface::class . '`');
        }
    }
}
