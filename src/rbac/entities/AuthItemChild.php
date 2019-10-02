<?php

namespace albertborsos\ddd\rbac\entities;

use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\rbac\traits\AuthItemChildAttributeLabelsTrait;

/**
 * @OA\Schema(
 *    schema="AuthItemChild",
 *    @OA\Property(property="parent", type="string"),
 *    @OA\Property(property="child", type="string"),
 * ),
 */

/**
 * Class AuthItemChild
 * @package albertborsos\ddd\rbac\entities
 */
class AuthItemChild extends AbstractEntity
{
    use AuthItemChildAttributeLabelsTrait;

    public $parent;
    public $child;

    /** @var AuthItem */
    public $parent0;
    /** @var AuthItem */
    public $child0;

    public function getPrimaryKey()
    {
        return ['parent', 'child'];
    }

    public function fields()
    {
        return [
            'parent',
            'child',
        ];
    }

    public function extraFields()
    {
        return [
            'parent0',
            'child0',
        ];
    }
}
