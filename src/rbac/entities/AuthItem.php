<?php

namespace albertborsos\ddd\rbac\entities;

use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\rbac\traits\AuthItemAttributeLabelsTrait;

/**
 * @OA\Schema(
 *    schema="AuthItem",
 *    @OA\Property(property="name", type="string"),
 *    @OA\Property(property="type", type="integer"),
 *    @OA\Property(property="description", type="string"),
 *    @OA\Property(property="ruleName", type="string"),
 *    @OA\Property(property="data", type="string"),
 *    @OA\Property(property="createdAt", ref="#/components/schemas/Entity/properties/createdAt"),
 *    @OA\Property(property="updatedAt", ref="#/components/schemas/Entity/properties/updatedAt"),
 * ),
 */

/**
 * Class AuthItem
 * @package albertborsos\ddd\rbac\entities
 */
class AuthItem extends AbstractEntity
{
    use AuthItemAttributeLabelsTrait;

    public $name;
    public $type;
    public $description;
    public $ruleName;
    public $data;
    public $createdAt;
    public $updatedAt;

    /** @var AuthAssignment[] */
    public $authAssignments;
    /** @var AuthRule */
    public $rule;
    /** @var AuthItem[] */
    public $children;
    /** @var AuthItem[] */
    public $parents;

    public function getPrimaryKey()
    {
        return ['name'];
    }


    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    public function fields()
    {
        return [
            'name',
            'type',
            'description',
            'ruleName',
            'data',
            'createdAt',
            'updatedAt',
        ];
    }

    public function extraFields()
    {
        return [
            'authAssignments',
            'rule',
            'children',
            'parents',
        ];
    }
}
