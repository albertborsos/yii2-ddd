<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\domains\page\interfaces\PageImageActiveRepositoryInterface;
use albertborsos\ddd\tests\support\base\domains\page\mysql\PageImageActiveRepository;
use albertborsos\ddd\tests\support\base\services\page\forms\DeletePageForm;

class DeletePageService extends AbstractPageService
{
    public function __construct(Page $entity, $config = [])
    {
        parent::__construct(null, $entity, $config);
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        /** @var Page $entity */
        $entity = $this->getEntity();

        /** @var PageImageActiveRepositoryInterface $repository */
        $repository = \Yii::createObject(PageImageActiveRepositoryInterface::class);
        $images = $repository->findAllByPage($entity);

        foreach ($images as $image) {
            $service = new DeletePageImageService($image);
            if (!$service->execute()) {
                return false;
            }
        }

        return $this->getRepository()->delete($entity);
    }
}
