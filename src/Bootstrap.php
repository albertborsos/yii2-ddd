<?php

namespace albertborsos\ddd;

use albertborsos\ddd\hydrators\ActiveHydrator;
use albertborsos\ddd\hydrators\ZendHydrator;
use albertborsos\ddd\interfaces\HydratorInterface;
use albertborsos\ddd\traits\SetContainerDefinitionsTrait;
use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package albertborsos\ddd
 * @since 2.0.0
 */
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
     *      albertborsos\ddd\interfaces\HydratorInterface::class => albertborsos\ddd\hydrators\ZendHydrator::class,
     *  ];
     * ```
     *
     * @return array
     */
    protected static function getContainerDefinitions(): array
    {
        return [
            HydratorInterface::class => ZendHydrator::class,
        ];
    }
}
