<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Optional for pivot models, but can be useful

class DoctorSpeciality extends Pivot
{
    use HasUuids, HasFactory; // Using HasFactory is optional for pivot models

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_speciality';

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
        'doctor_id',
        'speciality_id',
        'created_by', // Assuming these are foreign keys to a users table or similar
        'updated_by',
        'deleted_by', // Though 'deleted_by' is unusual for a pivot that isn't soft deleted
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // No specific casts needed based on the schema, unless created_by etc. are booleans
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['id'];
    }

    /**
     * Get the doctor associated with this pivot record.
     */
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Get the speciality associated with this pivot record.
     */
    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'speciality_id');
    }

    // If created_by, updated_by, deleted_by link to a User model:
    /*
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
    */
}