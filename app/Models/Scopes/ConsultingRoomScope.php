<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ConsultingRoomScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(auth()->user() && auth()->user()->hasRole('doctor') && auth()->user()->practitioner) {  // el doctor solo ve los clientes que tiene asociados
            $builder->whereHas('branch',function ($q){
               $q->whereIn('client_id',auth()->user()->clients()->pluck('client_id'));
            });

        }
    }
}
