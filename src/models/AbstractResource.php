<?php

namespace albertborsos\ddd\models;

use albertborsos\ddd\interfaces\BusinessObject;
use albertborsos\ddd\interfaces\FormObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

/**
 * Class AbstractResource
 * @deprecated since 0.3.0, will be removed in 1.0.0.
 * @package albertborsos\ddd\models
 */
abstract class AbstractResource extends AbstractModel
{
    /**
     * AbstractResource constructor.
     * @param FormObject|null $form
     * @param BusinessObject|null $model
     * @throws InvalidConfigException
     */
    public function __construct(FormObject $form = null, BusinessObject $model = null)
    {
        parent::__construct($form, $model);
        $this->initializeId();
    }

    /**
     * Sets the resource's id for update scenario
     *
     * @throws InvalidConfigException if `id` attribute not exist in BusinessObject
     */
    protected function initializeId()
    {
        if ($this->getModel() === null) {
            return;
        }

        if (!$this->getModel()->hasAttribute('id')) {
            throw new InvalidConfigException('Business object must have an id attribute or you need to override AbstractResource::initializeId method');
        }

        $this->setId($this->getModel()->id);
    }

    /**
     * @return mixed
     */
    public function save()
    {
        if ($this->getModel()) {
            return $this->getModel()->isNewRecord ? $this->insert() : $this->update();
        }
        return $this->insert();
    }

    /**
     * Business logic to store data.
     *
     * @return bool
     */
    abstract protected function insert();

    /**
     * Business logic to update data.
     *
     * @return bool
     */
    abstract protected function update();

    /**
     * Business logic to delete data.
     *
     * @return bool
     */
    abstract public function delete();

    /**
     * @return array
     */
    protected function getAttributes()
    {
        return $this->getModel()->getAttributes();
    }

    /**
     * Converts the model into an array.
     *
     * This method will first identify which fields to be included in the resulting array by calling [[resolveFields()]].
     * It will then turn the model into an array with these fields. If `$recursive` is true,
     * any embedded objects will also be converted into arrays.
     *
     * If the model implements the [[Linkable]] interface, the resulting array will also have a `_link` element
     * which refers to a list of links as specified by the interface.
     *
     * @param array $fields the fields being requested. If empty, all fields as specified by [[fields()]] will be returned.
     * @param array $expand the additional fields being requested for exporting. Only fields declared in [[extraFields()]]
     * will be considered.
     * @param bool $recursive whether to recursively return array representation of embedded objects.
     * @return array the array representation of the object
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        $data = [];
        foreach ($this->resolveFields($fields, $expand) as $field => $definition) {
            $data[$field] = is_string($definition) ? $this->getModel()->$definition : call_user_func($definition, $this, $field);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        return $recursive ? ArrayHelper::toArray($data) : $data;
    }
}
