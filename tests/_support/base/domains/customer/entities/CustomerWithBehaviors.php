<?php

namespace albertborsos\ddd\tests\support\base\domains\customer\entities;

use albertborsos\ddd\behaviors\BlameableBehavior;
use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\models\AbstractEntity;

/**
 * Class Customer
 * @package albertborsos\ddd\tests\support\base\domains\customer\entities
 */
class CustomerWithBehaviors extends AbstractEntity
{
    public $id;
    public $name;
    public $createdAt;
    public $createdBy;
    public $updatedAt;
    public $updatedBy;

    /** @var CustomerAddress[] */
    public $customerAddresses;

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'createdAt',
            'createdBy',
            'updatedAt',
            'updatedBy',
        ];
    }

    public function extraFields()
    {
        return [
            'customerAddresses',
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
            'customerAddresses' => CustomerAddress::class,
        ];
    }
}
