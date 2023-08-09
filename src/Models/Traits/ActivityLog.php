<?php
namespace JMolinas\Support\Models\Traits;

use JMolinas\Support\Services\Logger\Handlers\ModelHandler;
use JMolinas\Support\Services\Logger\Logger;
use Illuminate\Support\Facades\Auth;

trait ActivityLog
{
    /**
     * Boot function from laravel.
     */
    protected static function bootActivityLog()
    {
        if ($user = Auth::user()) {
            $activityLoger = new Logger(new ModelHandler());
            static::creating(
                function ($model) use ($activityLoger, $user) {
                    $activityLoger->creating($model, $user->id);
                }
            );
            static::updating(
                function ($model) use ($activityLoger, $user) {
                    $activityLoger->updating($model, $user->id);
                }
            );
            static::deleting(
                function ($model) use ($activityLoger, $user) {
                    $activityLoger->deleting($model, $user->id);
                }
            );
        }
    }
}