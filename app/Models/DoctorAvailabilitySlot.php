<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorAvailabilitySlot extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctor_availability_slots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'doctor_id',
        'slot_date',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'is_booked',
        'booked_by_appointment_id',
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
        'slot_date' => 'date',
        'start_time' => 'datetime:H:i:s', // Cast to datetime and format as time
        'end_time' => 'datetime:H:i:s',   // Cast to datetime and format as time
        'slot_duration_minutes' => 'integer',
        'is_booked' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the doctor that this availability slot belongs to.
     */
    public function doctor(): BelongsTo
    {
        // Assuming you have a Doctor model
        // Ensure the Doctor model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    /**
     * Get the appointment that booked this slot, if any.
     */
    public function bookingAppointment(): BelongsTo
    {
        // Assuming you have an Appointment model
        // Ensure the Appointment model also uses HasUuids if its primary key is a UUID
        return $this->belongsTo(Appointment::class, 'booked_by_appointment_id');
    }

    /**
     * Scope a query to only include available (not booked) slots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false);
    }

    /**
     * Scope a query to only include booked slots.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBooked($query)
    {
        return $query->where('is_booked', true);
    }
}
