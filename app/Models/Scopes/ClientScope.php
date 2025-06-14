<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ClientScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(auth()->user() && auth()->user()->hasRole('doctor') && auth()->user()->practitioner) {  // el doctor solo ve los clientes que tiene asociados
            $builder->whereIn('id',auth()->user()->clients()->pluck('client_id'));
        }elseif(auth()->user() && auth()->user()->hasRole('admin client')){
            $builder->whereIn('idss',auth()->user()->clients()->pluck('client_id'));
        }
    }
}
