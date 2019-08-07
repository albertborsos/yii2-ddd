<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\entities;

use albertborsos\ddd\models\AbstractEntity;

/**
 * Class CustomerAddress
 * @package albertborsos\ddd\tests\support\base\domains\customer\entities
 */
class CustomerAddress extends AbstractEntity
{
    public $id;
    public $customerId;
    public $zipCode;
    public $city;
    public $street;

    /** @var Customer */
    public $customer;

    public function fields()
    {
        return [
            'id',
            'customerId',
            'zipCode',
            'city',
            'street',
        ];
    }

    public function extraFields()
    {
        return [
            'customer',
        ];
    }

    /**
     * Mapping of property keys to entity classnames.
     *
     * @return array
     */
    public function relationMapping(): array
    {
        return [
            'customer' => Customer::class,
        ];
    }
}
