<?php

namespace albertborsos\ddd;

use albertborsos\ddd\hydrators\Hydrator;
use albertborsos\ddd\interfaces\HydratorInterface;
use albertborsos\ddd\traits\SetContainerDefinitionsTrait;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Bootstrap implements BootstrapInterface
{
    use SetContainerDefinitionsTrait;

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $this->setContainerDefinitions($app);
    }

    /**
     * Returns the DI Container definitions map in an array, with key-value pairs.
     * The keys are the Interface names and the values are the class names of the desired implementation of the interface.
     *
     * example:
     *
     * ```
     *  return [
     *      albertborsos\ddd\interfaces\HydratorInterface::class => albertborsos\ddd\hydrators\Hydrator::class,
     *  ];
     * ```
     *
     * @return array
     */
    protected static function getContainerDefinitions()
    {
        return [
            HydratorInterface::class => Hydrator::class,
        ];
    }
}
