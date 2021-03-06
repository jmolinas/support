<?php

namespace Gp\Support\Models;

use Illuminate\Database\Eloquent\Model;

class GaAuth extends Model
{
    protected $table = 'ga_auth';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'secret',
        'backup_code'
    ];

    /**
     * Related User
     *
     * @return @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
