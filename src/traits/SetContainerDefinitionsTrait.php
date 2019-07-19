<?php

namespace albertborsos\ddd\traits;

use Yii;
use yii\base\Application;

trait SetContainerDefinitionsTrait
{
    /**
     * Returns the DI Container definitions map in an array, with key-value pairs.
     * The keys are the Interface names and the values are the class names of the desired implementation of the interface.
     *
     * example:
     *
     * ```
     *  return [
     *      albertborsos\ddd\interfaces\HydratorInterface::class => albertborsos\ddd\hydrators\ActiveHydrator::class,
     *  ];
     * ```
     *
     * @return array
     */
    abstract protected static function getContainerDefinitions();

    /**
     * Sets the DI Container definitions for the application.
     *
     * @param Application $app
     * @param bool $overwrite if it is true, then it will overwrite the definition for the existing keys.
     */
    protected function setContainerDefinitions(Application $app, $overwrite = false)
    {
        foreach (static::getContainerDefinitions() as $interface => $class) {
            if (Yii::$container->has($interface) && !$overwrite) {
                continue;
            }

            $app->setContainer(['definitions' => [ltrim($interface, '\\') => ltrim($class, '\\')]]);
        }
    }
}
