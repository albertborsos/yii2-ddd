<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\customer;

use albertborsos\ddd\tests\support\base\domains\customer\traits\CustomerAttributeLabelsTrait;

/**
 * This is the model class for table "{{%customer}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 *
 * @property CustomerAddress[] $customerAddresses
 */
class Customer extends \yii\db\ActiveRecord
{
    use CustomerAttributeLabelsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerAddresses()
    {
        return $this->hasMany(CustomerAddress::className(), ['customer_id' => 'id'])->inverseOf('customer');
    }
}
