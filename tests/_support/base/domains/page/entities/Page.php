<?php

namespace albertborsos\ddd\tests\support\base\domains\page\entities;

use albertborsos\ddd\behaviors\BlameableBehavior;
use albertborsos\ddd\behaviors\TimestampBehavior;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\tests\support\base\domains\page\behaviors\PageSluggableBehavior;
use yii\helpers\Url;

/**
 * Class Page
 * @package albertborsos\ddd\tests\support\base\domains\page\entities
 */
class Page extends AbstractEntity
{
    const STATUS_VISIBLE = 1;

    public $id;
    public $name;
    public $category;
    public $title;
    public $description;
    public $date;
    public $slug;
    public $sortOrder;
    public $createdAt;
    public $createdBy;
    public $updatedAt;
    public $updatedBy;
    public $status;

    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
            'blameable' => BlameableBehavior::class,
            'sluggable' => PageSluggableBehavior::class,
        ];
    }

    public function fields()
    {
        return [
            'id',
            'name',
            'category',
            'title',
            'description',
            'date',
            'slug',
            'sortOrder',
            'createdAt',
            'createdBy',
            'updatedAt',
            'updatedBy',
            'status',
        ];
    }

    public function getUrl()
    {
        return Url::to(['/' . $this->slug]);
    }
}
