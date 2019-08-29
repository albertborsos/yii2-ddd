<?php

namespace albertborsos\ddd\tests\support\base\services\page\forms;

class CreatePageSlugForm extends AbstractPageSlugForm
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            // [['pageId', 'slug', 'status'], 'required'],
        ]);
    }
}
