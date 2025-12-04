<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class News extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'description', 'image_url', 'status'];
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('deleted_status', function (Builder $builder) {
            $builder->where('status', '!=', 'DELETED');
        });
    }

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     */
    public function delete()
    {
        $this->status = 'DELETED';
        return $this->save();
    }
}
