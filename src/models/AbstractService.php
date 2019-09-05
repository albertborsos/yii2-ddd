<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;

/**
 * Class AbstractService
 * @package albertborsos\ddd\models
 */
abstract class AbstractService extends Component
{
    /**
     * @var string|RepositoryInterface
     * @since 2.0.0
     */
    protected $repository;

    /**
     * The ID of the (main) object
     * @var integer|mixed
     */
    private $_id;

    /**
     * @var \yii\base\Model|FormObject
     */
    private $_form;

    /**
     * @var EntityInterface
     */
    private $_entity;

    public function __construct(FormObject $form = null, EntityInterface $entity = null, $config = [])
    {
        if ($form) {
            $this->setForm($form);
        }
        if ($entity) {
            $this->setEntity($entity);
        }
        if ($this->repository) {
            $repository = \Yii::createObject($this->repository);
            $this->setRepository($repository);
        }
        parent::__construct($config);
    }

    /**
     * @return boolean
     */
    abstract public function execute(): bool;

    /**
     * @return FormObject|\yii\base\Model
     */
    protected function getForm(): ?FormObject
    {
        return $this->_form;
    }

    /**
     * @return EntityInterface
     */
    protected function getEntity(): ?EntityInterface
    {
        return $this->_entity;
    }

    /**
     * @return RepositoryInterface
     * @since 2.0.0
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param $id
     */
    protected function setId($id): void
    {
        $this->_id = $id;
    }

    /**
     * @return int|array
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param FormObject $form
     */
    private function setForm(FormObject $form): void
    {
        $this->_form = $form;
    }

    /**
     * @param EntityInterface $entity
     */
    private function setEntity(EntityInterface $entity): void
    {
        $this->_entity = $entity;
    }

    /**
     * @param RepositoryInterface $repository
     */
    protected function setRepository(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }

    protected function hasEntity(): bool
    {
        return !empty($this->getEntity());
    }
}
