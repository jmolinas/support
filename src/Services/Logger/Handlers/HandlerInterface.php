<?php

namespace JMolinas\Support\Services\Logger\Handlers;

use Illuminate\Database\Eloquent\Model;

interface HandlerInterface
{
    public function handle($level, Model $model, array $record);
}