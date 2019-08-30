<?php

namespace albertborsos\ddd\tests\support\base\services\page\forms;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\traits\ActiveFormTrait;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageActiveRepositoryInterface;

abstract class AbstractPageForm extends Page implements FormObject
{
    use ActiveFormTrait;

    /** @var string|ActiveRepositoryInterface */
    protected $repository = PageActiveRepositoryInterface::class;

    public function rules()
    {
        return [
            [['name'], 'filter', 'filter' => function ($value) {
                return ucwords(preg_replace('/látványterv$/', '', strtolower($value)));
            }],
            [['name', 'category', 'title', 'description', 'date', 'status'], 'trim'],
            [['name', 'category', 'title', 'description', 'date', 'status'], 'default'],
            [['name', 'description', 'date', 'status'], 'required'],

            [['description'], 'string'],
            [['date'], 'safe'],
            [['status'], 'integer'],
            [['name', 'category', 'title'], 'string', 'max' => 255],
        ];
    }
}
