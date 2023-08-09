<?php

namespace JMolinas\Support\Services\Logger\Processors\ModelProcessors;

use JMolinas\Support\Services\Logger\Processors\ProcessorInterface;
use Illuminate\Database\Eloquent\Model;

class Updating implements ProcessorInterface
{
    use LogChanges;

    protected $oldAttributes, $model;

    public function __invoke(Model $model)
    {
        $this->model = $model;
        $oldValues = (new $model)->setRawAttributes($model->getRawOriginal());
        $this->oldAttributes = $this->logChanges($oldValues);
        return $this->attributeValuesToBeLogged();
    }

    protected function shouldLogOnlyDirty(): bool
    {
        if (!isset($this->model::$logOnlyDirty)) {
            return false;
        }

        return $this->model::$logOnlyDirty;
    }

    public function attributeValuesToBeLogged(): array
    {
        if (!count($this->attributesToBeLogged())) {
            return [];
        }

        $properties['attributes'] = $this->logChanges($this->model);
        $nullProperties = array_fill_keys(array_keys($properties['attributes']), null);
        $properties['old'] = array_merge($nullProperties, $this->oldAttributes);
        $this->oldAttributes = [];

        if ($this->shouldLogOnlyDirty()) {
            return $this->getDirty($properties, true);
        }
        $properties['diff'] = $this->getDirty($properties);
        return $properties;
    }

    protected function getDirty(array $properties, bool $isDirty = false)
    {
        $diff = [];
        if (isset($properties['old'])) {
           $diff['to'] = $properties['attributes'] = array_udiff_assoc(
                $properties['attributes'],
                $properties['old'],
                function ($new, $old) {
                    if ($old === null || $new === null) {
                        return $new === $old ? 0 : 1;
                    }

                    return $new <=> $old;
                }
            );
            $diff['from'] = $properties['old'] = collect($properties['old'])
                ->only(array_keys($properties['attributes']))
                ->all();
        }
        return $isDirty ? $properties : $diff;
    }
}
