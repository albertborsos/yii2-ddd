<?php

namespace albertborsos\ddd\rest\cache;

use albertborsos\ddd\rest\FormAndServiceActionTrait;
use albertborsos\ddd\rest\UpdateActionTrait;

/**
 * Class UpdateAction
 * @package albertborsos\ddd\rest\cache
 * @since 1.1.0
 */
class UpdateAction extends Action
{
    use FormAndServiceActionTrait;
    use UpdateActionTrait;
}
