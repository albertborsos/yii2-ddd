<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\rest\DeleteActionTrait;
use albertborsos\ddd\rest\FormAndServiceActionTrait;

/**
 * Class DeleteAction
 * @package albertborsos\ddd\rest\active
 * @since 2.0.0
 */
class DeleteAction extends Action
{
    use FormAndServiceActionTrait;
    use DeleteActionTrait;
}
