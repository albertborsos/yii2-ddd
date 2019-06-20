<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\FormObject;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class AbstractModel
 * @package albertborsos\ddd\models
 */
abstract class AbstractModel extends Component
{
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

    /**
     * @var array
     */
    private $_errors;

    /**
     * @return array
     */
    abstract protected function getAttributes();

    public function __construct(FormObject $form = null, BusinessObject $model = null)
    {
        if ($form) {
            $this->setForm($form);
        }
        if ($model) {
            $this->setModel($model);
        }
        parent::__construct([]);
    }

    /**
     * @param FormObject $form
     */
    private function setForm(FormObject $form)
    {
        $this->_form = $form;
    }

    /**
     * @return FormObject|\yii\base\Model
     */
    protected function getForm()
    {
        return $this->_form;
    }

    /**
     * @param BusinessObject $model
     */
    private function setModel(BusinessObject $model)
    {
        $this->_model = $model;
    }

    /**
     * @return BusinessObject|ActiveRecord
     */
    protected function getModel()
    {
        return $this->_model;
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
     * @param array $errors
     */
    protected function addErrors(array $errors)
    {
        $this->_errors = ArrayHelper::merge($this->_errors, $errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }
    /**
     * @return array
     */
    public function getFirstErrors()
    {
        if (empty($this->_errors)) {
            return [];
        }

        $errors = [];
        foreach ($this->_errors as $name => $es) {
            if (!empty($es)) {
                $errors[$name] = reset($es);
            }
        }
        return $errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->getErrors());
    }
}
