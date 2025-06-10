<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PractitionerScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if(auth()->user() && auth()->user()->hasRole('admin client')) {  // el doctor solo ve los clientes que tiene asociados
            $builder->whereHas('user',function ($q){
                $q->whereHas('clients',function ($q2){
                    $q2->whereIn('user_clients.client_id',auth()->user()->clients()->pluck('client_id'));
                });
            });
        }
    }
}
