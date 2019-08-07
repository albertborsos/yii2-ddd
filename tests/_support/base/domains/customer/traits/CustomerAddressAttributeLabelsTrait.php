<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\traits;

use Yii;

trait CustomerAddressAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'zip_code' => 'Zip Code',
            'city' => 'City',
            'street' => 'Street',
        ];
    }
}
