<?php

namespace albertborsos\ddd\rest\cache;

use albertborsos\ddd\rest\CreateActionTrait;
use albertborsos\ddd\rest\FormAndServiceActionTrait;

class CreateAction extends Action
{
    use FormAndServiceActionTrait;
    use CreateActionTrait;
}
