<?php

namespace albertborsos\ddd\rest\active;

use albertborsos\ddd\rest\DeleteActionTrait;
use albertborsos\ddd\rest\FormAndServiceActionTrait;

class DeleteAction extends Action
{
    use FormAndServiceActionTrait;
    use DeleteActionTrait;
}
