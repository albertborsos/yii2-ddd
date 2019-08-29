<?php

namespace albertborsos\ddd\tests\support\base\domains\page\mysql;

/**
 * This is the ActiveQuery class for [[PageSlug]].
 *
 * @see PageSlug
 */
class PageSlugQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return PageSlug[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return PageSlug|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
