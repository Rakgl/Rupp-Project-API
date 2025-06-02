<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'rating',
        'comment',
        'is_approved',
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
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the appointment associated with the review.
     */
    public function appointment(): BelongsTo
    {
        // Assuming you have an Appointment model
        // Ensure the Appointment model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }

    /**
     * Get the patient who wrote the review.
     */
    public function patient(): BelongsTo
    {
        // Assuming you have a Patient model
        // Ensure the Patient model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the doctor who was reviewed.
     */
    public function doctor(): BelongsTo
    {
        // Assuming you have a Doctor model
        // Ensure the Doctor model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Scope a query to only include approved reviews.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include not approved (pending) reviews.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotApproved($query)
    {
        return $query->where('is_approved', false);
    }
}
