<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\customer;

use albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\customer\CustomerWithBehaviorsRepositoryInterface;
use Cycle\ORM\Schema;

class CustomerWithBehaviorsRepository extends CustomerRepository implements CustomerWithBehaviorsRepositoryInterface
{
    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\customer\entities\CustomerWithBehaviors::class;

    public static function schema(): array
    {
        $schema = parent::schema();
        $schema[Schema::ENTITY] = CustomerWithBehaviors::class;
        $schema[Schema::COLUMNS] = [
            'id',
            'name',
            'slug',
            'createdAt' => 'created_at',
            'createdBy' => 'created_by',
            'updatedAt' => 'updated_at',
            'updatedBy' => 'updated_by',
        ];
        $schema[Schema::TYPECAST] = [
            'id' => 'int',
            'createdAt' => 'int',
            'createdBy' => 'int',
            'updatedAt' => 'int',
            'updatedBy' => 'int',
        ];
        return $schema;
    }
}
