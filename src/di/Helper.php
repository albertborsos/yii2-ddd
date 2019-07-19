<?php

namespace albertborsos\ddd\di;

/**
 * Class Helper is a wrapper for `Yii::createObject()` with extended fuctionality.
 * It allows to call a static method of an interface.
 *
 * For example:
 *
 * ```
 * \Yii::createObject([CustomerActiveRepositoryInterface::class, 'dataModelClass']);
 * ```
 *
 * It will calls the `dataModelClass` method of the class which configured for the `CustomerActiveRepositoryInterface` in `Yii::$container`
 *
 * @see https://github.com/yiisoft/yii2/pull/17419
 * @package albertborsos\ddd\di
 * @since 1.1.0
 */
class Helper
{
    public static function createObject($type, array $params = [])
    {
        try {
            return \Yii::createObject($type, $params);
        } catch (\TypeError $e) {
            if ($object = self::callAsInterface($type, $params)) {
                return $object;
            }
            throw $e;
        }
    }

    /**
     * @param $type
     * @param array $params
     * @return object
     * @throws InvalidConfigException
     */
    private static function callAsInterface($type, array $params)
    {
        if (is_callable($type, true) && !is_callable($type)) {
            $type[0] = get_class(static::createObject($type[0]));
            return \Yii::createObject($type, $params);
        }

        return false;
    }
}
