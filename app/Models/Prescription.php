<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prescriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'appointment_id',
        'patient_id',
        'doctor_id',
        'prescription_date',
        'diagnosis',
        'general_advice',
        'follow_up_date',
        'pharmacy_id',
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
        'prescription_date' => 'date',
        'follow_up_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the appointment associated with the prescription.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class); // Assuming you have an Appointment model
    }

    /**
     * Get the patient associated with the prescription.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class); // Assuming you have a Patient model
    }

    /**
     * Get the doctor associated with the prescription.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class); // Assuming you have a Doctor model
    }

    /**
     * Get the pharmacy associated with the prescription.
     */
    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class); // Assuming you have a Pharmacy model
    }

    /**
     * Get the medication entries for this prescription.
     */
    public function prescriptionMedications(): HasMany
    {
        return $this->hasMany(PrescriptionMedication::class);
    }

    /**
     * Get the medications included in this prescription.
     */
    public function medications()
    {
        return $this->belongsToMany(Medication::class, 'prescription_medications')
                    ->using(PrescriptionMedication::class) // Optional: if you want to access pivot model instance
                    ->withPivot(['id', 'dosage', 'frequency', 'duration', 'quantity_prescribed', 'instructions', 'created_by', 'updated_by', 'deleted_by'])
                    ->withTimestamps();
    }
}