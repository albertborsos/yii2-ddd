<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\mysql\page;

use albertborsos\ddd\tests\support\base\domains\page\traits\PageSlugAttributeLabelsTrait;

/**
 * This is the model class for table "{{%page_slug}}".
 *
 * @property int $id
 * @property int $page_id
 * @property string $slug
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 * @property int $status
 *
 * @property Page $page
 */
class PageSlug extends \yii\db\ActiveRecord
{
    use PageSlugAttributeLabelsTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%page_slug}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['page_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'status'], 'integer'],
            [['slug'], 'string', 'max' => 255],
            [['page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::class, 'targetAttribute' => ['page_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id'])->inverseOf('pageSlugs');
    }

    /**
     * {@inheritdoc}
     * @return PageSlugQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PageSlugQuery(get_called_class());
    }
}
