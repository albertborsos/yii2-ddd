<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cache\customer;

class CustomerAddressModifiedSchemaRepository extends CustomerAddressRepository
{
    public static function columns(): array
    {
        return [
            'id',
            'customerId' => 'customer_id',
            'zipCode' => 'zip_code',
            'city',
        ];
    }
}
