<?php

namespace albertborsos\ddd\tests\unit\behaviors;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\models\AbstractEntity;
use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\domains\page\entities\PageSlug;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageRepositoryInterface;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface;
use albertborsos\ddd\tests\support\base\services\page\AbstractPageService;
use albertborsos\ddd\tests\support\base\services\page\CreatePageService;
use albertborsos\ddd\tests\support\base\services\page\forms\CreatePageForm;
use albertborsos\ddd\tests\support\base\services\page\forms\UpdatePageForm;
use albertborsos\ddd\tests\fixtures\PageFixture;
use albertborsos\ddd\tests\fixtures\PageSlugFixture;
use albertborsos\ddd\tests\support\base\services\page\UpdatePageService;
use Codeception\PHPUnit\TestCase;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\test\FixtureTrait;

class AbstractUniqueSluggableBehaviorTest extends TestCase
{
    use FixtureTrait;

    public function fixtures()
    {
        return [
            'page' => PageFixture::class,
            'pageSlugs' => PageSlugFixture::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->initFixtures();
        \Yii::$app->cycle->cleanHeap();
    }

    public function newSlugDataProvider()
    {
        return [
            'normal' => ['konyhabutor', 'Konyhabútor'],
//            'special letters' => ['arvizturotukorfurogep', 'Árvíztűrőtükörfúrógép'],
        ];
    }

    /**
     * @dataProvider newSlugDataProvider
     *
     * @param $name
     * @param string $title
     * @param string $description
     * @throws \yii\base\InvalidConfigException
     */
    public function testNewSlug($expectedSlug, $name, $title = 'Title', $description = 'Description', $status = Page::STATUS_VISIBLE)
    {
        $service = $this->mockService(compact('name', 'title', 'description', 'status'));
        $this->assertTrue($service->execute());
        $this->assertNotNull($service->getId());

        $page = $this->getPageRepository()->findById($service->getId());

        $this->assertEquals($expectedSlug, $page->slug);
    }

    public function newExistingSlugDataprovider()
    {
        return [
            'exists as page slug' => ['page1'],
            'exists as archived page_slug' => ['page3'],
            'exists as page slug and archived page_slug' => ['page2'],
        ];
    }

    /**
     * @dataProvider newExistingSlugDataprovider
     */
    public function testNewSlugWhichAlreadyExists($existingSlugPageAlias)
    {
        $pageSlugRepository = $this->getPageSlugRepository();
        /** @var Page $existingPage */
        $existingPage = $this->findPageByAlias($existingSlugPageAlias);
        $existingSlugs = array_merge([$existingPage->slug], ArrayHelper::getColumn($pageSlugRepository->findAllByPage($existingPage), 'slug'));

        $data = ['name' => $existingPage->name, 'title' => 'Title', 'description' => 'Description', 'status' => Page::STATUS_VISIBLE];

        $service = $this->mockService($data);
        $this->assertTrue($service->execute());

        $page = $this->findPageById($service->getId());

        $this->assertNotContains($page->slug, $existingSlugs);
    }

    public function testUpdateWithoutModification()
    {
        /** @var Page $page */
        $pageId = $this->getPageIdByAlias('page1');
        $page = $this->findPageById($pageId);
        $oldSlug = $page->slug;

        $service = $this->mockService([], $page);

        $this->assertTrue($service->execute());
        $this->assertEquals($page->id, $service->getId());

        /** @var Page $page */
        $page = $this->findPageById($pageId);

        $this->assertEquals($oldSlug, $page->slug);
    }

    public function updateToAnExistingSlugInOtherPageDataProvider()
    {
        return [
            'exists only as page slug' => ['page2', 'page1', 'First Page', 'first-page-2'],
            'exists only as archived page_slug' => ['page1', 'page3', 'Third Page', 'third-page-2'],
            'exists as page slug and archived page_slug' => ['page1', 'page2', 'Second Page', 'second-page-3'],
        ];
    }

    /**
     * @dataProvider updateToAnExistingSlugInOtherPageDataProvider
     * @param $updatePageAlias
     * @param $existingSlugPageAlias
     * @param $newName
     * @param $expectedSlug
     * @throws \yii\base\InvalidConfigException
     */
    public function testUpdateToASlugWhichExistsInOtherPage($updatePageAlias, $existingSlugPageAlias, $newName, $expectedSlug)
    {
        /** @var Page $updatePage */
        $updatePage = $this->findPageByAlias($updatePageAlias);
        $oldSlug = $updatePage->slug;
        $existingSlugPageId = $this->getPageIdByAlias($existingSlugPageAlias);
        $existingSlugPage = $this->findPageById($existingSlugPageId);
        $existingSlugs = array_merge([$existingSlugPage->slug], ArrayHelper::getColumn($this->findAllPageSlugsByPageId($existingSlugPageId), 'slug'));

        $data = ['name' => $newName];

        $service = $this->mockService($data, $updatePage);
        $this->assertTrue($service->execute());
        $this->assertEquals($updatePage->id, $service->getId());
        $updatedPage = $this->findPageById($service->getId());

        $this->assertEquals($expectedSlug, $updatedPage->slug);
        $this->assertNotEquals($expectedSlug, $oldSlug);
        $this->assertNotContains($updatedPage->slug, $existingSlugs);
    }


    public function updateToAnExistingSlugInCurrentPageDataProvider()
    {
        return [
            'exists only as page slug' => ['page1', 'First Page', 'first-page'],
            'exists only as archived page_slug' => ['page3', 'Third Page', 'third-page'],
            'exists as page slug and archived page_slug' => ['page4', 'Fourth Page', 'fourth-page'],
        ];
    }

    /**
     * @dataProvider updateToAnExistingSlugInCurrentPageDataProvider
     * @param $updatePageAlias
     * @param $newName
     * @param $expectedSlug
     * @throws \yii\base\InvalidConfigException
     */
    public function testUpdateToASlugWhichExistInCurrentPage($updatePageAlias, $newName, $expectedSlug)
    {
        /** @var Page $updatePage */
        $updatePage = $this->findPageByAlias($updatePageAlias);
        $existingSlugs = array_merge([$updatePage->slug], ArrayHelper::getColumn($this->findAllPageSlugsByPageId($updatePage->id), 'slug'));

        $data = ['name' => $newName];

        $service = $this->mockService($data, $updatePage);
        $this->assertTrue($service->execute());
        $this->assertEquals($updatePage->id, $service->getId());

        $updatedPage = $this->findPageById($service->getId());

        $this->assertEquals($expectedSlug, $updatePage->slug);
        $this->assertContains($updatedPage->slug, $existingSlugs);
    }

    /**
     * @param $data
     * @param AbstractEntity|EntityInterface|null $entity
     * @return AbstractPageService
     * @throws \yii\base\InvalidConfigException
     */
    protected function mockService($data, EntityInterface $entity = null): AbstractPageService
    {
        $form = empty($entity)
            ? new CreatePageForm($data)
            : new UpdatePageForm(array_merge($entity->getAttributes(['id', 'name', 'title', 'description', 'date', 'status']), $data));

        if (!$form->validate()) {
            throw new InvalidArgumentException('Invalid values passed to form:' . Json::encode($form->getErrors()));
        }

        return empty($entity) ? new CreatePageService($form) : \Yii::createObject(UpdatePageService::class, [$form, $entity]);
    }

    protected function getPageRepository(): PageRepositoryInterface
    {
        return \Yii::createObject(PageRepositoryInterface::class);
    }

    protected function getPageSlugRepository(): PageSlugRepositoryInterface
    {
        return \Yii::createObject(PageSlugRepositoryInterface::class);
    }

    /**
     * @param $pageAlias
     * @return Page|null
     */
    protected function findPageByAlias($pageAlias): Page
    {
        return $this->findPageById($this->getPageIdByAlias($pageAlias));
    }

    /**
     * @param $pageAlias
     * @return string
     */
    protected function getPageIdByAlias($pageAlias): string
    {
        return $this->getFixture('page')[$pageAlias]['id'];
    }
    /**
     * @param $id
     * @return Page|null
     */
    protected function findPageById($id): Page
    {
        return $this->getPageRepository()->findById($id);
    }

    /**
     * @param $pageSlugRepository
     * @param $existingSlugPageId
     * @return PageSlug[]
     */
    protected function findAllPageSlugsByPageId($existingSlugPageId): array
    {
        return $this->getPageSlugRepository()->findAllByPageId($existingSlugPageId);
    }
}
