<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\CacheRepositoryInterface;
use albertborsos\ddd\interfaces\EntityInterface;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class AbstractRepository
 * @package albertborsos\ddd\repositories
 * @since 1.1.0
 */
abstract class AbstractCacheRepository extends Component implements CacheRepositoryInterface
{
    /**
     * @return string
     */
    abstract protected static function entityModelClass(): string;

    /**
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!\Yii::createObject(static::entityModelClass()) instanceof EntityInterface) {
            throw new Exception(get_called_class() . '::entityModelClass() must implements `albertborsos\ddd\interfaces\EntityInterface`');
        }
    }
}
