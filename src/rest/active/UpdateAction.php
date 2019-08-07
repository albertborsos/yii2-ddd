<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\rest\FormAndServiceActionTrait;
use albertborsos\ddd\rest\UpdateActionTrait;

/**
 * Class UpdateAction
 * @package albertborsos\ddd\rest\active
 * @since 2.0.0
 */
class UpdateAction extends Action
{
    use FormAndServiceActionTrait;
    use UpdateActionTrait;
}
