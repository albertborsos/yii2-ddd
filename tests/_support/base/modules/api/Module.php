<?php

namespace albertborsos\ddd\tests\support\base\modules\admin;

use mito\cms\core\traits\RegisterSubmodulesTrait;
use yii\base\Application;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    use RegisterSubmodulesTrait;

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $this->registerSubModules($app, $this);
    }

    /**
     * Returns the submodules to bootstrap in an array with key-value pairs.
     * The keys are the IDs of the submodules, and the keys are the classnames of the submodules.
     *
     * fore example:
     *
     * ```
     *  return [
     *      'v1' => \albertborsos\ddd\tests\support\base\modules\admin\v1\Module::class,
     *  ];
     * ```
     *
     * @return array
     */
    protected static function getSubModules()
    {
        return [
            'v1' => \albertborsos\ddd\tests\support\base\modules\admin\v1\Module::class,
        ];
    }
}
