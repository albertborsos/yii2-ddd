<?php

namespace albertborsos\ddd\tests\support\base\services\page\forms;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\traits\ActiveFormTrait;
use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageActiveRepositoryInterface;

abstract class AbstractPageSlugForm extends PageSlug implements FormObject
{
    use ActiveFormTrait;

    /** @var string|ActiveRepositoryInterface */
    protected $repository = PageSlugActiveRepositoryInterface::class;

    public function rules()
    {
        return [
            [['pageId', 'slug', 'status'], 'trim'],
            [['pageId', 'slug', 'status'], 'default'],
            [['pageId', 'slug', 'status'], 'required'],

            [['pageId', 'status'], 'integer'],
            [['slug'], 'string', 'max' => 255],
            [['pageId'], 'exist', 'skipOnError' => true, 'targetClass' => $this->getRepository(PageActiveRepositoryInterface::class)->getDataModelClass(), 'targetAttribute' => ['pageId' => 'id']],
        ];
    }
}
