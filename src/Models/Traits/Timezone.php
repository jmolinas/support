<?php

namespace JMolinas\Support\Models\Traits;

use Illuminate\Http\Request;
use Carbon\Carbon;

trait Timezone
{
    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        $request = new Request();
        $timezone = $request->header('X-timezone', env('APP_LOCAL_TIMEZONE', 'UTC'));
        if ($value && $this->isDateAttribute($key)) {
            $value = Carbon::parse($value, 'UTC')->timezone($timezone);
        }
        return parent::setAttribute($key, $value);
    }
}
