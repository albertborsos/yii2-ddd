<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\page;

use albertborsos\cycle\Factory;
use albertborsos\cycle\SchemaInterface;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface;
use Cycle\ORM\Relation;
use yii\data\BaseDataProvider;

class PageRepository extends AbstractCycleRepository implements PageRepositoryInterface, SchemaInterface
{
    protected $entityClass = Page::class;

    public static function columns(): array
    {
        return ['id', 'name', 'category', 'title', 'description', 'date', 'slug', 'sortOrder' => 'sort_order', 'createdAt' => 'created_at', 'createdBy' => 'created_by', 'updatedAt' => 'updated_at', 'updatedBy' => 'updated_by', 'status'];
    }

    public static function schema(): array
    {
        return Factory::schema(
            Page::class,
            'page',
            'id',
            static::columns(),
            ['id' => 'int'],
            [
                'pageSlugs' => Factory::relation(Relation::HAS_MANY, 'page', 'id', 'page_id'),
            ]
        );
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        // TODO: Implement search() method.
    }
}
