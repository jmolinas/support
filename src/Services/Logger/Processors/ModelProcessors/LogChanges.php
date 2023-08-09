<?php

namespace JMolinas\Support\Services\Logger\Processors\ModelProcessors;

use JMolinas\Support\Services\Logger\Exceptions\CouldNotLogChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait LogChanges
{
    protected static function getRelatedModelAttributeValue(Model $model, string $attribute): array
    {
        if (substr_count($attribute, '.') > 1) {
            throw CouldNotLogChanges::invalidAttribute($attribute);
        }

        [$relatedModelName, $relatedAttribute] = explode('.', $attribute);

        $relatedModelName = Str::camel($relatedModelName);

        $relatedModel = $model->$relatedModelName ?? $model->$relatedModelName();

        return ["{$relatedModelName}.{$relatedAttribute}" => $relatedModel->$relatedAttribute ?? null];
    }

    protected function isDateAttribute($model, $key)
    {
        return in_array($key, $model->getDates(), true) ||
                                    $this->isDateCastable($model, $key);
    }

    public function isDateCastable($model, $key)
    {
        $types =  ['date', 'datetime'];
        if (array_key_exists($key, $model->getCasts())) {
            return $types ? in_array($this->getCastType($model, $key), (array) $types, true) : true;
        }

        return false;
    }

    protected function isCustomDateTimeCast($cast)
    {
        return strncmp($cast, 'date:', 5) === 0 ||
               strncmp($cast, 'datetime:', 9) === 0;
    }

    protected function getCastType($model, $key)
    {
        if ($this->isCustomDateTimeCast($model->getCasts()[$key])) {
            return 'custom_datetime';
        }

        if ($this->isDecimalCast($model->getCasts()[$key])) {
            return 'decimal';
        }

        return trim(strtolower($model->getCasts()[$key]));
    }

    /**
     * Determine if the cast type is a decimal cast.
     *
     * @param  string  $cast
     * @return bool
     */
    protected function isDecimalCast($cast)
    {
        return strncmp($cast, 'decimal:', 8) === 0;
    }

    protected static function getModelAttributeJsonValue(Model $model, string $attribute)
    {
        $path = explode('->', $attribute);
        $modelAttribute = array_shift($path);
        $modelAttribute = collect($model->getAttribute($modelAttribute));

        return data_get($modelAttribute, implode('.', $path));
    }

    protected function attributesToBeLogged(): array
    {
        $attributes = [];

        if (isset($this->model::$logFillable) && $this->model::$logFillable) {
            $attributes = array_merge($attributes, $this->model->getFillable());
        }

        if ($this->shouldLogUnguarded()) {
            $attributes = array_merge($attributes, array_diff(array_keys($this->model->getAttributes()), $this->model->getGuarded()));
        }

        if (isset($this->model::$logAttributes) && is_array($this->model::$logAttributes)) {
            $attributes = array_merge($attributes, array_diff($this->model::$logAttributes, ['*']));

            if (in_array('*', $this->model::$logAttributes)) {
                $attributes = array_merge($attributes, array_keys($this->model->getAttributes()));
            }
        }

        if (isset($this->model::$logAttributesToIgnore) && is_array($this->model::$logAttributesToIgnore)) {
            $attributes = array_diff($attributes, $this->model::$logAttributesToIgnore);
        }

        return $attributes;
    }


    public function shouldLogUnguarded(): bool
    {
        if (!isset($this->model::$logUnguarded)) {
            return false;
        }

        if (!$this->model::$logUnguarded) {
            return false;
        }

        if (in_array('*', $this->model->getGuarded())) {
            return false;
        }

        return true;
    }

    public function logChanges(Model $model): array
    {
        $changes = [];
        $attributes = $this->attributesToBeLogged();

        foreach ($attributes as $attribute) {
            if (Str::contains($attribute, '.')) {
                $changes += self::getRelatedModelAttributeValue($model, $attribute);

                continue;
            }

            if (Str::contains($attribute, '->')) {
                Arr::set(
                    $changes,
                    str_replace('->', '.', $attribute),
                    static::getModelAttributeJsonValue($model, $attribute)
                );

                continue;
            }

            $changes[$attribute] = $model->getAttribute($attribute);

            if (is_null($changes[$attribute])) {
                continue;
            }

            if ($this->isDateAttribute($model, $attribute)) {
                $changes[$attribute] = $model->serializeDate(
                    $model->asDateTime($changes[$attribute])
                );
            }

            if ($model->hasCast($attribute)) {
                $cast = $model->getCasts()[$attribute];

                if ($this->isCustomDateTimeCast($cast)) {
                    $changes[$attribute] = $model->asDateTime($changes[$attribute])->format(explode(':', $cast, 2)[1]);
                }
            }
        }

        return $changes;
    }
}