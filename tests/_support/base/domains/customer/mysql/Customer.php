<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\mysql;

use albertborsos\ddd\tests\support\base\domains\customer\traits\CustomerAttributeLabelsTrait;

/**
 * This is the model class for table "{{%customer}}".
 *
 * @property int $id
 * @property string $name
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
            [['name'], 'string', 'max' => 255],
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
