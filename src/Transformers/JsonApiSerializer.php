<?php

namespace GP\Support\Transformers;

use Illuminate\Database\Eloquent\Model;

class JsonApiSerializer implements TransformerInterface
{
    /**
     * Data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Model
     *
     * @var Model
     */
    protected $model;

    /**
     * Transform
     *
     * @param Model $item
     * @param mixed $key
     * @param string $type
     *
     * @return callable
     */
    public function transform(Model $item, $key = null, $type = null)
    {
        $this->model = $item;
        $this->getType($type);
        $keyName = $item->getKeyName();
        $mutated = $item->getMutatedAttributes();
        $attributes = $item->getAttributes();

        foreach ($mutated as $value) {
            $attributes[$value] = $item->getAttribute($value);
        }

        if ($item->{$keyName} !== null) {
            $this->data['id'] = $item->{$keyName};
            unset($attributes[$keyName]);
        }

        if (!empty($item->getRelations())) {
            $this->data['relationships'] = $item->getRelations();
        }
        $this->data['attributes'] = $attributes;
        return $this;
    }

    /**
     * Get Type
     *
     * @param string $type
     *
     * @return JsonApiSerializer
     */
    public function getType($type = null)
    {
        $className = explode('\\', get_class($this->model));
        $type = $type === null ? strtolower(end($className)) : $type;
        $this->data = ['type' => $type];
        return $this;
    }

    /**
     * Get Model
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
