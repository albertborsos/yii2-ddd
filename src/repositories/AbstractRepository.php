<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\Exception;

/**
 * Class AbstractRepository
 * @package albertborsos\ddd\repositories
 * @since 1.1.0
 */
abstract class AbstractRepository extends Component implements RepositoryInterface
{
    /**
     * @return string
     */
    abstract protected static function businessModelClass(): string;

    /**
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!\Yii::createObject(static::businessModelClass()) instanceof BusinessObject) {
            throw new Exception(get_called_class() . '::businessModelClass() must implements `albertborsos\ddd\interfaces\BusinessObject`');
        }
    }
}
