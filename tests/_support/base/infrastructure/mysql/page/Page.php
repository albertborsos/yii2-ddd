<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\page;

use albertborsos\ddd\tests\support\base\domains\page\traits\PageAttributeLabelsTrait;

/**
 * This is the model class for table "{{%page}}".
 *
 * @property int $id
 * @property string $name
 * @property string $category
 * @property string $title
 * @property string $description
 * @property string $date
 * @property string $slug
 * @property int $sort_order
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 * @property int $status
 *
 * @property PageImage[] $pageImages
 */
class Page extends \yii\db\ActiveRecord
{
    use PageAttributeLabelsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['date'], 'safe'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'sort_order', 'status'], 'integer'],
            [['name', 'category', 'title', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     * @return PageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PageQuery(get_called_class());
    }
}
