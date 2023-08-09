<?php

namespace JMolinas\Support\Services\Logger\Processors;

use Illuminate\Database\Eloquent\Model;

interface ProcessorInterface
{
    /**
     * @return array The processed record
     */
    public function __invoke(Model $model);
}
