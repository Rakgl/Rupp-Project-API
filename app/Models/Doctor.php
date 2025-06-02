<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes
use App\Traits\DataQuery; // Assuming this trait exists
use App\Traits\DataScope;  // Assuming this trait exists

class Doctor extends Model
{
    use HasFactory, HasUuids, SoftDeletes, DataQuery, DataScope;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctors';

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
        'user_id',
        'title',
        'registration_number',
        'bio',
        'gender',
        'date_of_birth',
        'consultation_fee',
        'currency_code',
        'years_of_experience',
        'qualifications', // Store as JSON or comma-separated
        'profile_picture_path',
        'is_verified',
        'is_available_for_consultation',
        'availability_status',
        'average_rating',
        'hospital_id',
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
        'date_of_birth' => 'date',
        'is_verified' => 'boolean',
        'is_available_for_consultation' => 'boolean',
        'average_rating' => 'decimal:1', // Cast to decimal with 1 digit after the point
        'qualifications' => 'json', // Assuming qualifications are stored as JSON
        'years_of_experience' => 'integer',
    ];

    /**
     * The attributes that should be mutated to dates.
     * This is for SoftDeletes.
     * @var array
     */
    protected $dates = ['deleted_at'];

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
     * Get the user that owns the doctor profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the hospital associated with the doctor.
     */
    public function hospital()
    {
        // Assuming you have a Hospital model
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    /**
     * The specialities that belong to the doctor.
     */
	public function specialities()
	{
		return $this->belongsToMany(Speciality::class, 'doctor_speciality', 'doctor_id', 'speciality_id')->where('specialities.status', 'ACTIVE');
	}
}