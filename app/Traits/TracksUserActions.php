<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

trait TracksUserActions
{
    protected static function bootTracksUserActions()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'created_by')) {
                    $model->created_by = Auth::id();
                }
                if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'updated_by')) {
                    $model->updated_by = Auth::id();
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        // Only apply deleting event if the model uses SoftDeletes
        if (in_array(SoftDeletes::class, class_uses_recursive(get_called_class()))) {
            static::deleting(function ($model) {
                if (Auth::check()) {
                    // Ensure this is only set if the model is actually being soft deleted
                    // and not permanently deleted.
                    if (!$model->isForceDeleting()) {
                        if ($model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'deleted_by')) {
                            $model->deleted_by = Auth::id();
                            $model->save(); // Important: save the model before soft delete proceeds
                        }
                    }
                }
            });
        }
    }
}