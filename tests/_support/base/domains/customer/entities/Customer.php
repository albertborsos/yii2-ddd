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

    /** @var CustomerAddress[] */
    public $customerAddresses;

    public function fields()
    {
        return [
            'id',
            'name',
        ];
    }

    public function extraFields()
    {
        return [
            'customerAddresses',
        ];
    }
}
