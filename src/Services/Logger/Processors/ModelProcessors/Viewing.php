<?php

namespace JMolinas\Support\Services\Logger\Processors\ModelProcessors;

use JMolinas\Support\Services\Logger\Processors\ProcessorInterface;
use Illuminate\Database\Eloquent\Model;

class Viewing implements ProcessorInterface
{
    use LogChanges;

    public function __invoke(Model $model)
    {
        $this->model = $model;
        return $this->logChanges($model);
    }
}
