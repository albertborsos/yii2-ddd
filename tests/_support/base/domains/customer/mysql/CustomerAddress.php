<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\tests\support\base\domains\customer\traits\CustomerAddressAttributeLabelsTrait;

/**
 * This is the model class for table "{{%customer_address}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $zip_code
 * @property string $city
 * @property string $street
 *
 * @property Customer $customer
 */
class CustomerAddress extends \yii\db\ActiveRecord
{
    use CustomerAddressAttributeLabelsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer_address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'zip_code'], 'integer'],
            [['city', 'street'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id'])->inverseOf('customerAddresses');
    }
}
