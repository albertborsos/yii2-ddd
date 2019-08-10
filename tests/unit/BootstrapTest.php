<?php

namespace albertborsos\ddd\tests\unit;

use albertborsos\ddd\Bootstrap;
use albertborsos\ddd\tests\support\base\BootstrapWithDefinitionOverride;
use Codeception\PHPUnit\TestCase;

class BootstrapTest extends TestCase
{
    public function testBootstrap()
    {
        $bootstrap = (new Bootstrap())->bootstrap(\Yii::$app);
    }

    public function testBootstrapWithOverride()
    {
        $bootstrap = (new BootstrapWithDefinitionOverride())->bootstrap(\Yii::$app);
    }
}
