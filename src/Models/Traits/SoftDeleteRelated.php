<?php

namespace JMolinas\Support\Models\Traits;

trait SoftDeleteRelated
{
    /**
     * Boot function from laravel.
     */
    protected static function bootSoftDeleteRelated()
    {
        static::deleting(
            function ($model) {
                if (!empty($relations = $model->getRelations())) {
                    foreach ($relations as $key => $value) {
                        $model->{$key}()->delete();
                    }
                }
            }
        );
    }
}
