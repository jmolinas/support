<?php

namespace JMolinas\Support\Services\Logger\Handlers;

use JMolinas\Support\Models\Logs;
use JMolinas\Support\Services\Logger\Processors\ModelProcessors\Creating;
use JMolinas\Support\Services\Logger\Processors\ModelProcessors\Deleting;
use JMolinas\Support\Services\Logger\Processors\ModelProcessors\Updating;
use JMolinas\Support\Services\Logger\Processors\ModelProcessors\Viewing;
use Illuminate\Database\Eloquent\Model;

class ModelHandler extends ProcessingModelHandlers implements HandlerInterface
{
    /**
     * Called when writing to our database
     */
    protected function write(array $record) : void
    {
        Logs::create($record);
    }

    protected function updating(Model $model)
    {
        $processor = new Updating();
        return call_user_func($processor, $model);
    }

    protected function creating(Model $model)
    {
        $processor = new Creating();
        return call_user_func($processor, $model);
    }

    protected function deleting(Model $model)
    {
        $processor = new Deleting();
        return call_user_func($processor, $model);
    }

    protected function viewing(Model $model)
    {
        $processor = new Viewing();
        return call_user_func($processor, $model);
    }
}