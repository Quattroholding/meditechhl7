<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    use SoftDeletes;

    /**
     * Convert value to string, handling enums properly
     */
    protected static function valueToString($value): string
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return (string) $value;
    }

    public static function boot()
    {
        parent::boot();
        static::updating(function ($model) {
            // do some logging
            // override some property like $model->something = transform($something);
            $changes = $model->isDirty() ? $model->getDirty() : false;

            if (count($changes) > 0) {
                $user_id = User::first()->id;
                $user_name = 'Administrador Del Sistema';
                if (Auth::check()) {
                    $user_id = Auth::user()->id;
                    $user_name = Auth::user()->full_name;
                }

                foreach ($changes as $attr => $value) {
                    if ($model->getOriginal($attr) != $model->$attr && ! in_array($attr, ['note', 'DIAGNOSTIC_DESCRIPTION']) && ! is_array($model->$attr) && ! is_array($model->getOriginal($attr))) {
                        $oldValue = self::valueToString($model->getOriginal($attr));
                        $newValue = self::valueToString($model->$attr);

                        $accion = "Se modifico la columna ($attr) : de [{$oldValue}] a [{$newValue}]";

                        // Registrar cambio en UserLog
                        UserLog::create([
                            'user_id' => $user_id,
                            'user_name' => $user_name,
                            'tabla' => $model->getTable(),
                            'table_id' => $model->getKey(),
                            'action' => 'update',
                            'columna' => $attr,
                            'old_value' => $oldValue,
                            'new_value' => $newValue,
                            'observation' => $accion,
                        ]);

                        // Registrar cambio de estado en StatusHistoryLog
                        if (($attr == 'status' or $attr == 'estatus') && ! empty($model->$attr)) {
                            StatusHistoryLog::create([
                                'table_name' => $model->getTable(),
                                'model_name' => class_basename(get_class($model)),
                                'record_id' => $model->getKey(),
                                'user_id' => $user_id,
                                'new_status' => $newValue,
                                'old_status' => $oldValue,
                            ]);
                        }
                    }

                }
            }

        });
        static::created(function ($model) {
            // do some logging
            // override some property like $model->something = transform($something);
            $user_id = User::first()->id;
            $user_name = 'Administrador Del Sistema';
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $user_name = Auth::user()->full_name;
            }

            return UserLog::create([
                'user_id' => $user_id,
                'user_name' => $user_name,
                'tabla' => $model->getTable(),
                'table_id' => $model->getKey(),
                'action' => 'new',
                'columna' => '',
                'old_value' => '',
                'new_value' => '',
                'observation' => 'Registro Creado',
            ]);

            if (Schema::hasColumn($model->getTable(), 'status') && ! empty($model->status)) {
                $statusValue = self::valueToString($model->status);
                StatusHistoryLog::create([
                    'table_name' => $model->getTable(),
                    'model_name' => class_basename(get_class($model)),
                    'record_id' => $model->getKey(),
                    'user_id' => $user_id,
                    'new_status' => $statusValue,
                ]);
            }
        });
        static::deleted(function ($model) {
            // do some logging
            // override some property like $model->something = transform($something);
            $user_id = User::first()->id;
            $user_name = 'Administrador Del Sistema';
            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $user_name = Auth::user()->full_name;
            }

            return UserLog::create([
                'user_id' => $user_id,
                'user_name' => $user_name,
                'tabla' => $model->getTable(),
                'table_id' => $model->getKey(),
                'action' => 'Registro eliminado',
                'columna' => '',
                'old_value' => '',
                'new_value' => '',
                'observation' => 'Registro Creado',
            ]);
        });
    }

    public function getCreatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d-m-Y'); // Change the format to whichever you desire
    }

    public function getUpdatedAtAttribute($attr)
    {
        return Carbon::parse($attr)->format('d-m-Y'); // Change the format to whichever you desire
    }

    public function files()
    {
        return $this->hasMany(File::class, 'record_id')->where('table_name', $this->getTable());
    }
}
