<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerAddress;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerRepositoryInterface;
use albertborsos\ddd\validators\ExistValidator;

abstract class AbstractCustomerAddressForm extends CustomerAddress implements FormObject
{
    public function rules()
    {
        return [
            [['customerId', 'zipCode', 'city', 'street'], 'trim'],
            [['customerId', 'zipCode', 'city', 'street'], 'default'],
            [['customerId', 'zipCode', 'city', 'street'], 'required'],

            [['city', 'street'], 'string', 'max' => 255],

            [['customerId'], ExistValidator::class, 'targetRepository' => CustomerRepositoryInterface::class, 'targetAttribute' => ['customerId' => 'id']],
        ];
    }
}
