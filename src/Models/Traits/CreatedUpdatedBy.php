<?php

namespace JMolinas\Support\Models\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait CreatedUpdatedBy
{
    /**
     * Boot function from laravel.
     */
    protected static function bootCreatedUpdatedBy()
    {
        static::creating(function ($model) {
            $user = Auth::user();
            $model->created_by = $user->id;
            $columns = Schema::getColumnListing($model->getTable());
            if (in_array('updated_by', $columns)) {
                $model->updated_by = $user->id;
            }
        });
        static::updating(function ($model) {
            $user = Auth::user();
            $model->updated_by = $user->id;
        });
    }
}
