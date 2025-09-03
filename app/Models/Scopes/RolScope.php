<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user() && auth()->user()->hasRole('doctor') && auth()->user()->practitioner) {
            $builder->whereIn('id', [3]);
        } elseif (auth()->user() && auth()->user()->hasRole('admin client')) {
            $builder->whereIn('id', [5]);
        }
    }
}
