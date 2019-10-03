<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\entities;

use albertborsos\ddd\models\AbstractEntity;

/**
 * Class Customer
 * @package albertborsos\ddd\tests\support\base\domains\customer\entities
 */
class Customer extends AbstractEntity
{
    public $id;
    public $name;
    public $status;

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETED = 2;

    /** @var CustomerAddress[] */
    public $customerAddresses;

    public function fields()
    {
        return [
            'id',
            'name',
            'status',
        ];
    }

    public function extraFields()
    {
        return [
            'customerAddresses',
        ];
    }
}
