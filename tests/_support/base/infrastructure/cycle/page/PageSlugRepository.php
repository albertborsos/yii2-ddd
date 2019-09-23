<?php

namespace albertborsos\ddd\tests\support\base\infrastructure\cycle\page;

use albertborsos\cycle\Factory;
use albertborsos\cycle\SchemaInterface;
use albertborsos\ddd\repositories\AbstractCycleRepository;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface;
use Cycle\ORM\Relation;
use yii\data\BaseDataProvider;

class PageSlugRepository extends AbstractCycleRepository implements PageSlugRepositoryInterface, SchemaInterface
{
    protected $entityClass = \albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug::class;

    public static function schema(): array
    {
        return Factory::schema(PageSlug::class, 'page_slug', 'id', ['id', 'pageId' => 'page_id', 'slug', 'createdAt' => 'created_at', 'createdBy' => 'created_by', 'updatedAt' => 'updated_at', 'updatedBy' => 'updated_by', 'status'], ['id' => 'int'], [
            'page' => Factory::relation(Relation::HAS_ONE, 'page', 'lazy', 'page_id', 'id'),
        ]);
    }

    public function findAllByPage(Page $page): array
    {
        return $this->findAllByPageId($page->id);
    }

    public function findAllByPageId($pageId): array
    {
        return $this->find()->andWhere(['page_id' => $pageId])->fetchAll();
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
