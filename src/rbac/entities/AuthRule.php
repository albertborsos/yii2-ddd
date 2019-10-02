<?php

namespace albertborsos\ddd\rbac\entities;

use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\rbac\traits\AuthRuleAttributeLabelsTrait;

/**
 * @OA\Schema(
 *    schema="AuthRule",
 *    @OA\Property(property="name", type="string"),
 *    @OA\Property(property="data", type="string"),
 *    @OA\Property(property="createdAt", ref="#/components/schemas/Entity/properties/createdAt"),
 *    @OA\Property(property="updatedAt", ref="#/components/schemas/Entity/properties/updatedAt"),
 * ),
 */

/**
 * Class AuthRule
 * @package albertborsos\ddd\rbac\entities
 */
class AuthRule extends AbstractEntity
{
    use AuthRuleAttributeLabelsTrait;

    public $name;
    public $data;
    public $createdAt;
    public $updatedAt;

    /** @var AuthItem[] */
    public $authItems;

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
            'data',
            'createdAt',
            'updatedAt',
        ];
    }

    public function extraFields()
    {
        return [
            'authItems',
        ];
    }
}
