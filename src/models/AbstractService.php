<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\FormObject;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

/**
 * Class AbstractDomain
 * @package albertborsos\ddd\models
 */
abstract class AbstractService extends Component
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
}
