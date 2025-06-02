<?php

namespace App\Models;

use App\Traits\DataQuery;
use App\Traits\DataScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory, SoftDeletes, HasUuids, DataScope, DataQuery;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointments';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'hospital_id',
        'appointment_datetime',
        'duration_minutes',
        'status',
        'reason_for_visit',
        'doctor_notes',
        'patient_notes',
        'meeting_link',
        'cancellation_reason',
        'cancelled_by',
        'consultation_fee_charged',
        'payment_status',
        'payment_gateway_transaction_id',
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
        'appointment_datetime' => 'datetime',
        'duration_minutes' => 'integer',
        'consultation_fee_charged' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the patient that owns the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    /**
     * Get the doctor that owns the appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Get the hospital associated with the appointment.
     */
    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id');
    }

    /**
     * Get the user who created the appointment.
     * Assuming you have a User model.
     */
    public function createdByUser(): BelongsTo
    {
        // Replace App\Models\User::class with your actual User model namespace if different
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the appointment.
     * Assuming you have a User model.
     */
    public function updatedByUser(): BelongsTo
    {
        // Replace App\Models\User::class with your actual User model namespace if different
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who soft deleted the appointment.
     * Assuming you have a User model.
     */
    public function deletedByUser(): BelongsTo
    {
        // Replace App\Models\User::class with your actual User model namespace if different
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the user who cancelled the appointment.
     * Assuming you have a User model.
     */
    public function cancelledByUser(): BelongsTo
    {
        // Replace App\Models\User::class with your actual User model namespace if different
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Scope a query to only include scheduled appointments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to only include confirmed appointments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include completed appointments.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

}
