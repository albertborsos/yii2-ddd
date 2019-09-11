<?php

namespace albertborsos\ddd\tests\support\base\services\page\forms;

use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageActiveRepositoryInterface;
use albertborsos\ddd\validators\ExistValidator;

abstract class AbstractPageSlugForm extends PageSlug implements FormObject
{
    public function rules()
    {
        return [
            [['pageId', 'slug', 'status'], 'trim'],
            [['pageId', 'slug', 'status'], 'default'],
            [['pageId', 'slug', 'status'], 'required'],

            [['pageId', 'status'], 'integer'],
            [['slug'], 'string', 'max' => 255],
            [['pageId'], ExistValidator::class, 'targetRepository' => PageActiveRepositoryInterface::class, 'targetAttribute' => ['pageId' => 'id']],
        ];
    }
}
