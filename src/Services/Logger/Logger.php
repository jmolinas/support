<?php

namespace JMolinas\Support\Services\Logger;

use JMolinas\Support\Services\Logger\Handlers\HandlerInterface;
use Illuminate\Database\Eloquent\Model;

class Logger
{
    public const UPDATING = 'updating';

    public const CREATING = 'creating';

    public const DELETING = 'deleting';

    public const VIEWING = 'viewing';

    private $handler, $record = [];

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function updating(Model $model, $causer, array $metadata = [])
    {
        $this->recordBuilder($model, $causer, $metadata);
        return $this->addRecord(static::UPDATING, $model);
    }

    public function creating(Model $model, $causer, array $metadata = [])
    {
        $this->recordBuilder($model, $causer, $metadata);
        return $this->addRecord(static::CREATING, $model);
    }

    public function deleting(Model $model, $causer, array $metadata = [])
    {
        $this->recordBuilder($model, $causer, $metadata);
        return $this->addRecord(static::DELETING, $model);
    }

    public function viewing(Model $model, $causer, array $metadata = [])
    {
        $this->recordBuilder($model, $causer, $metadata);
        return $this->addRecord(static::VIEWING, $model);
    }

    protected function recordBuilder(Model $model, $causer, array $metadata = [])
    {
        $key = $model->getKeyName();
        $record = [
            'entity_id' => $model->{$key},
            'user_id' => $causer
        ];
        if (! empty($metadata)) {
            $record['metadata'] = json_encode($metadata);
        }
        $this->record = $record;
    }

    protected function addRecord($level, $model)
    {
        return $this->handler->handle($level, $model, $this->record);
    }
}
