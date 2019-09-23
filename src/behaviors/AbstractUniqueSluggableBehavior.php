<?php

namespace albertborsos\ddd\behaviors;

use yii\base\InvalidConfigException;

abstract class AbstractUniqueSluggableBehavior extends SluggableBehavior
{
    public $ensureUnique = true;

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
    abstract protected function uniqueValidators();

    protected function checkRepositoryIsConfigured(): void
    {
        if ($this->ensureUnique && empty($this->repository) && empty($this->uniqueValidators())) {
            throw new InvalidConfigException(get_called_class() . '::$repository or ' . get_called_class() . '::uniqueValidators() must be set!');
        }
    }

    protected function validateSlug($slug)
    {
        foreach ($this->uniqueValidators() as $validator) {
            $this->uniqueValidator = $validator;
            $isUnique = parent::validateSlug($slug);
            if (!$isUnique) {
                return false;
            }
        }

        return true;
    }
}
