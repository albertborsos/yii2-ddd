<?php

namespace albertborsos\ddd\tests\support\base\services\customer\forms;

use albertborsos\ddd\interfaces\ActiveRepositoryInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\traits\ActiveFormTrait;
use mito\cms\core\validators\HtmlPurifierFilter;
use albertborsos\ddd\tests\support\base\domains\customer\entities\Customer;
use albertborsos\ddd\tests\support\base\domains\customer\interfaces\CustomerActiveRepositoryInterface;

abstract class AbstractCustomerForm extends Customer implements FormObject
{
    use ActiveFormTrait;

    /** @var string|ActiveRepositoryInterface */
    protected $repository = CustomerActiveRepositoryInterface::class;

    public function rules()
    {
        return [
            [['name'], HtmlPurifierFilter::class],
            [['name'], 'trim'],
            [['name'], 'default'],
            [['name'], 'required'],

            [['name'], 'string', 'max' => 255],
        ];
    }
}
