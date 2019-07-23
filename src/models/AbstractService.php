<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\interfaces\FormObject;
use albertborsos\ddd\interfaces\RepositoryInterface;
use yii\base\Component;
use yii\base\InvalidConfigException;

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
     */
    private $_model;

    public function __construct(FormObject $form = null, BusinessObject $model = null, $config = [])
    {
        if ($form) {
            $this->setForm($form);
        }
        if ($model) {
            $this->setModel($model);
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
     */
    protected function getModel()
    {
        return $this->_model;
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
     */
    private function setModel(BusinessObject $model)
    {
        $this->_model = $model;
    }

    /**
     * @param RepositoryInterface $repository
     */
    protected function setRepository(RepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }
}
