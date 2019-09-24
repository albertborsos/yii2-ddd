<?php

namespace albertborsos\ddd\tests\support\base\services\page;

use albertborsos\ddd\tests\support\base\domains\page\entities\Page;
use albertborsos\ddd\tests\support\base\services\page\forms\CreatePageSlugForm;
use albertborsos\ddd\tests\support\base\services\page\forms\UpdatePageForm;
use Cycle\ORM\Transaction;
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

        /** @var Transaction $transaction */
        $transaction = $this->getRepository()->beginTransaction();

        try {
            if (parent::execute()) {
                /** @var Page $entity */
                $entity = $this->getRepository()->findById($this->getId());
                if ($entity->slug !== $oldSlug) {
                    $this->storeOldSlug($this->getId(), $oldSlug);
                }
                $transaction->run();
                return true;
            }
        } catch (Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
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
