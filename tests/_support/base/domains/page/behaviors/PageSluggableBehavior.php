<?php

namespace albertborsos\ddd\tests\support\base\domains\page\behaviors;

use albertborsos\ddd\behaviors\AbstractUniqueSluggableBehavior;
use albertborsos\ddd\tests\support\base\domains\page\interfaces\PageActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\domains\page\interfaces\PageSlugActiveRepositoryInterface;

class PageSluggableBehavior extends AbstractUniqueSluggableBehavior
{
    public $attribute = 'name';

    /**
     * List of \yii\validators\UniqueValidator configurations to validate the uniqueness of a slug in multiple repositories.
     * The value of `targetClass` property is ridden from the actual repository.
     *
     * ```php
     *  [
     *      PageActiveRepositoryInterface::class     => ['targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? ['NOT', ['id' => $this->owner->id]] : []],
     *      PageSlugActiveRepositoryInterface::class => ['targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? ['NOT', ['page_id' => $this->owner->id]] : []],
     *  ]
     * ```
     *
     * @return array
     * @var array
     */
    protected function uniqueValidators()
    {
        return [
            PageActiveRepositoryInterface::class     => ['targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? ['NOT', ['id' => $this->owner->id]] : []],
            PageSlugActiveRepositoryInterface::class => ['targetAttribute' => 'slug', 'filter' => isset($this->owner->id) ? ['NOT', ['page_id' => $this->owner->id]] : []],
        ];
    }
}
