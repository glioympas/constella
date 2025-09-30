<?php

namespace Lioy\Constella\Actions;

use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionMethod;

class GetModelRelationsAction
{
    /**
     * @return array<int, string>
     */
    public function execute(Model $model): array
    {
        return collect(new ReflectionClass($model)->getMethods())
            ->filter(
                fn (ReflectionMethod $method) => ! empty($method->getReturnType()) &&
                    str_contains(
                        $method->getReturnType(),
                        'Illuminate\Database\Eloquent\Relations'
                    )
            )
            ->pluck('name')
            ->all();
    }
}
