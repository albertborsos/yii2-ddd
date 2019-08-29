<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\services\page\forms\CreatePageSlugForm;
use albertborsos\ddd\tests\support\base\services\page\forms\UpdatePageForm;
use yii\base\Exception;

class UpdatePageService extends AbstractPageService
{
    public function __construct(UpdatePageForm $form, Page $entity, $config = [])
    {
        parent::__construct($form, $entity, $config);
    }

    public function execute(): bool
    {
        $oldSlug = $this->getEntity()->slug;

        $transaction = $this->getRepository()->beginTransaction();

        try {
            if (parent::execute()) {
                /** @var Page $entity */
                $entity = $this->getRepository()->findOne($this->getId());
                if ($entity->slug !== $oldSlug) {
                    $this->storeOldSlug($this->getId(), $oldSlug);
                }
                $transaction->commit();
                return true;
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        return false;
    }

    private function storeOldSlug(int $pageId, $slug)
    {
        $form = new CreatePageSlugForm([
            'pageId' => $pageId,
            'slug' => $slug,
        ]);

        $service = new CreatePageSlugService($form);
        return $service->execute();
    }
}
