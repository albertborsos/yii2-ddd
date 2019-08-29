<?php

namespace albertborsos\ddd\tests\support\base\services\page\forms;

class UpdatePageForm extends AbstractPageForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['name', 'category', 'title', 'description', 'date', 'slug', 'status'], 'required'],
        ]);
    }
}
