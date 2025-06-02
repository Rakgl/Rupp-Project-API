<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot; // Important: extend Pivot
use Illuminate\Database\Eloquent\Factories\HasFactory; // Optional: if you want to use factories for the pivot

class PrescriptionMedication extends Pivot
{
    use HasUuids; // If you want this pivot model to also manage its own UUID key independently
    // use HasFactory; // Uncomment if you plan to create factories for this pivot model

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prescription_medications';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false; // Set to false because 'id' is a UUID

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string'; // Set to string for UUIDs

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', // Include 'id' if you intend to manually set it or fill it
        'prescription_id',
        'medication_id',
        'dosage',
        'frequency',
        'duration',
        'quantity_prescribed',
        'instructions',
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
        'quantity_prescribed' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the prescription that this entry belongs to.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Get the medication that this entry refers to.
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }
}