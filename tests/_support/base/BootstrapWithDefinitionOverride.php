<?php

namespace albertborsos\ddd\tests\support\base;

use albertborsos\ddd\Bootstrap;

class BootstrapWithDefinitionOverride extends Bootstrap
{
    public function bootstrap($app)
    {
        $this->setContainerDefinitions($app, true);
    }
}
