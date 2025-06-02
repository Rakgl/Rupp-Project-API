<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medical_records';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'uploaded_by_user_id',
        'appointment_id',
        'record_type',
        'file_path',
        'file_name',
        'mime_type',
        'description',
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the patient to whom this medical record belongs.
     */
    public function patient(): BelongsTo
    {
        // Assuming you have a Patient model
        // Ensure the Patient model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the user who uploaded this medical record.
     */
    public function uploadedByUser(): BelongsTo
    {
        // Assuming you have a User model
        // Ensure the User model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * Get the appointment associated with this medical record, if any.
     */
    public function appointment(): BelongsTo
    {
        // Assuming you have an Appointment model
        // Ensure the Appointment model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Appointment::class, 'appointment_id');
    }
}
