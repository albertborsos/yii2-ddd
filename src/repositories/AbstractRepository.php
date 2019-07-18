<?php

namespace albertborsos\ddd\repositories;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\HydratorInterface;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Class AbstractRepository
 * @package albertborsos\ddd\repositories
 * @since 1.1.0
 */
abstract class AbstractRepository extends Component implements RepositoryInterface
{
    /**
     * The Hydrator class which creates entities from data.
     *
     * @var string|HydratorInterface
     */
    protected $hydrator = HydratorInterface::class;

    /**
     * @return string
     */
    abstract public static function entityModelClass(): string;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!\Yii::createObject(static::entityModelClass()) instanceof EntityInterface) {
            throw new InvalidConfigException(get_called_class() . '::entityModelClass() must implements `' . EntityInterface::class . '`');
        }

        $fieldMap = \Yii::createObject([static::entityModelClass(), 'fieldMap']);
        $this->hydrator = \Yii::createObject($this->hydrator, [$fieldMap]);
        if (!$this->hydrator instanceof HydratorInterface) {
            throw new InvalidConfigException(get_called_class() . '::$hydrator must implements `' . HydratorInterface::class . '`');
        }
    }
}
