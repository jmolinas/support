<?php

namespace Gp\Support\Models;

class Roles extends CoreModel
{
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
}
