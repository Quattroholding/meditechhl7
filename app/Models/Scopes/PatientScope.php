<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PatientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(auth()->user() && (auth()->user()->hasRole('doctor') OR auth()->user()->hasRole('asistente'))){
            $builder->whereHas("clients",function ($q){
                $q->whereIn('client_id',auth()->user()->clients()->pluck('client_id'));
            });
        }
    }
}
