<?php

namespace albertborsos\ddd\tests\support\base\domains\page\behaviors;

use albertborsos\ddd\behaviors\AbstractUniqueSluggableBehavior;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface;

class PageSluggableBehavior extends AbstractUniqueSluggableBehavior
{
    public $attribute = 'name';

    /**
     * List of \yii\validators\UniqueValidator configurations to validate the uniqueness of a slug in multiple repositories.
     * The value of `targetClass` property is ridden from the actual repository.
     *
     * ```php
     *  [
     *      ['targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? [['id', '!=', $this->owner->id]] : []],
     *      ['targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? [['page_id', '!=', $this->owner->id]] : []],
     *  ]
     * ```
     *
     * @return array
     * @var array
     */
    protected function uniqueValidators()
    {
        return [
            ['targetRepository' => PageRepositoryInterface::class, 'targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? [['id', '!=', $this->owner->id]] : []],
            ['targetRepository' => PageSlugRepositoryInterface::class, 'targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? [['page_id', '!=', $this->owner->id]] : []],
        ];
    }
}
