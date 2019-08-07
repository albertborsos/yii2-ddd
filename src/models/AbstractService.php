<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;

/**
 * Class AbstractDomain
 * @package albertborsos\ddd\models
 */
abstract class AbstractService extends Component
{
    /**
     * @var string|RepositoryInterface
     * @since 1.1.0
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
     * @var \yii\db\ActiveRecord|BusinessObject
     * @deprecated since 1.1.0
     */
    private $_model;

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
    abstract public function execute();

    /**
     * @return FormObject|\yii\base\Model
     */
    protected function getForm()
    {
        return $this->_form;
    }

    /**
     * @return EntityInterface|BusinessObject
     * @deprecated since 1.1.0
     */
    protected function getModel()
    {
        return $this->_model;
    }

    /**
     * @return EntityInterface
     */
    protected function getEntity()
    {
        return $this->_entity;
    }

    /**
     * @return RepositoryInterface
     * @since 1.1.0
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param $id
     */
    protected function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param FormObject $form
     */
    private function setForm(FormObject $form)
    {
        $this->_form = $form;
    }

    /**
     * @param BusinessObject $model
     * @deprecated since 1.1.0
     */
    private function setModel(BusinessObject $model)
    {
        $this->_model = $model;
    }

    /**
     * @param EntityInterface $entity
     */
    private function setEntity(EntityInterface $entity)
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
}
