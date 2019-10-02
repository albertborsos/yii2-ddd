<?php

namespace albertborsos\ddd\rbac\entities;

use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\rbac\traits\AuthAssignmentAttributeLabelsTrait;

/**
 * @OA\Schema(
 *    schema="AuthAssignment",
 *    @OA\Property(property="itemName", type="string"),
 *    @OA\Property(property="userId", type="string"),
 *    @OA\Property(property="createdAt", ref="#/components/schemas/Entity/properties/createdAt"),
 * ),
 */

/**
 * Class AuthAssignment
 * @package albertborsos\ddd\rbac\entities
 */
class AuthAssignment extends AbstractEntity
{
    use AuthAssignmentAttributeLabelsTrait;

    public $itemName;
    public $userId;
    public $createdAt;

    /** @var AuthItem */
    public $item;

    public function getPrimaryKey()
    {
        return ['itemName', 'userId'];
    }

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
            'updatedAtAttribute' => false,
        ];
    }

    public function fields()
    {
        return [
            'itemName',
            'userId',
            'createdAt',
        ];
    }

    public function extraFields()
    {
        return [
            'item',
        ];
    }
}
