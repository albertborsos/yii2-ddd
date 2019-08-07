<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\rest\CreateActionTrait;
use albertborsos\ddd\rest\FormAndServiceActionTrait;

/**
 * Class CreateAction
 * @package albertborsos\ddd\rest\active
 * @since 2.0.0
 */
class CreateAction extends Action
{
    use FormAndServiceActionTrait;
    use CreateActionTrait;
}
