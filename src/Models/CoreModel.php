<?php

namespace Gp\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CoreModel extends Model
{
    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        $table = $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
        return config('schema.core') . ".{$table}";
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new static((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );
        $model->setTable($this->table);

        return $model;
    }
}
