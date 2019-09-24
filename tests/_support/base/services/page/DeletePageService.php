<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\infrastructure\interfaces\page\PageSlugRepositoryInterface;

class DeletePageService extends AbstractPageService
{
    public function __construct(Page $entity, $config = [])
    {
        parent::__construct(null, $entity, $config);
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function execute(): bool
    {
        /** @var Page $entity */
        $entity = $this->getEntity();

        /** @var PageSlugRepositoryInterface $repository */
        $repository = \Yii::createObject(PageSlugRepositoryInterface::class);
        $slugs = $repository->findAllByPage($entity);

        foreach ($slugs as $slug) {
            $service = new DeletePageSlugService($slug);
            if (!$service->execute()) {
                return false;
            }
        }

        return $this->getRepository()->delete($entity);
    }
}
