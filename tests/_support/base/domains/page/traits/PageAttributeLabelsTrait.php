<?php

namespace albertborsos\ddd\tests\support\base\domains\page\traits;

use Yii;

trait PageAttributeLabelsTrait
{
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'category' => 'Category',
            'title' => 'Title',
            'description' => 'Description',
            'date' => 'Date',
            'slug' => 'Slug',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'status' => 'Status',
        ];
    }
}
