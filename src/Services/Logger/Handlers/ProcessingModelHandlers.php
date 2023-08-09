<?php

namespace JMolinas\Support\Services\Logger\Handlers;

use Illuminate\Database\Eloquent\Model;

abstract class ProcessingModelHandlers
{
    /**
     * {@inheritdoc}
     */
    public function handle($level, Model $model, array $record)
    {
        $record['entity'] = $model->getTable();
        $record['type'] = $level;
        $record['metadata'] = isset($record['metadata']) && !empty($record['metadata']) ?
             $record['metadata'] :
             json_encode($this->{$level}($model));
        return $this->write($record);
    }

    /**
     * Writes the record down to the log of the implementing handler
     */
    abstract protected function write(array $record): void;
}