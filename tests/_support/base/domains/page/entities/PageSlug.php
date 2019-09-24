<?php

namespace albertborsos\ddd\tests\support\base\domains\page\entities;

use albertborsos\ddd\behaviors\BlameableBehavior;
use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\models\AbstractEntity;

/**
 * Class PageSlug
 * @package albertborsos\ddd\tests\support\base\domains\page\entities
 */
class PageSlug extends AbstractEntity
{
    public $id;
    public $pageId;
    public $slug;
    public $createdAt;
    public $createdBy;
    public $updatedAt;
    public $updatedBy;
    public $status;

    /** @var Page */
    public $page;

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
            'pageId',
            'slug',
            'createdAt',
            'createdBy',
            'updatedAt',
            'updatedBy',
            'status',
        ];
    }

    public function extraFields()
    {
        return [
            'page',
        ];
    }
}
