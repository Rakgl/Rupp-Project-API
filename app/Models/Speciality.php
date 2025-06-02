<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\DataQuery; // Assuming this trait exists
use App\Traits\DataScope;  // Assuming this trait exists
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Speciality extends Model
{
    use HasFactory, HasUuids, DataQuery, DataScope;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Add casts if necessary, e.g., 'status' => 'boolean' if it's stored as integer
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id']; // Specify 'id' as the UUID column
    }

    /**
     * The doctors that belong to the speciality.
     */
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_speciality', 'speciality_id', 'doctor_id')
                    ->using(DoctorSpeciality::class) // Use the custom pivot model
                    ->withPivot('id', 'created_by', 'updated_by', 'deleted_by') // Include extra pivot columns from doctor_speciality table
                    ->withTimestamps(); // To manage created_at and updated_at on the pivot table
    }
}
