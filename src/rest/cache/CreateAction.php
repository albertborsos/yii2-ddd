<?php

namespace albertborsos\ddd\rest\cache;

use albertborsos\ddd\rest\CreateActionTrait;
use albertborsos\ddd\rest\FormAndServiceActionTrait;

/**
 * Class CreateAction
 * @package albertborsos\ddd\rest\cache
 * @since 1.1.0
 */
class CreateAction extends Action
{
    use FormAndServiceActionTrait;
    use CreateActionTrait;
}
