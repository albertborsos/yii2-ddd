<?php

namespace albertborsos\ddd\tests\support\base\services\page\forms;

class CreatePageForm extends AbstractPageForm
{
    public function init()
    {
        $this->date = date('Y-m-d');
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['name', 'category', 'title', 'description', 'date', 'slug', 'status'], 'required'],
        ]);
    }
}
