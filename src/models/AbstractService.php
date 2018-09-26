<?php

namespace albertborsos\ddd\models;

use yii\helpers\ArrayHelper;
use yii\web\Link;
use yii\web\Linkable;

/**
 * Class AbstractDomain
 * @package albertborsos\ddd\models
 */
abstract class AbstractService extends AbstractModel
{
    /**
     * @return boolean
     */
    abstract public function execute();

    /**
     * @return array
     */
    protected function getAttributes()
    {
        return $this->getForm()->getAttributes();
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
            $data[$field] = is_string($definition) ? $this->getForm()->$definition : call_user_func($definition, $this, $field);
        }

        if ($this instanceof Linkable) {
            $data['_links'] = Link::serialize($this->getLinks());
        }

        return $recursive ? ArrayHelper::toArray($data) : $data;
    }
}
