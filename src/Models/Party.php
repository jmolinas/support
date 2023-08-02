<?php

namespace JMolinas\Support\Models;

use Illuminate\Database\Eloquent\Model;

class Party extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'parties';

    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type'];

    /**
     * User Record Associated with Party
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'party_id', 'id');
    }
}
