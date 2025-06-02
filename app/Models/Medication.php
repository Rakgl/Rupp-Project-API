<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medication extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'generic_name',
        'manufacturer',
        'strength',
        'form',
        'description',
        'is_prescription_required',
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
        'is_prescription_required' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the prescription medication entries associated with this medication.
     */
    public function prescriptionMedications(): HasMany
    {
        return $this->hasMany(PrescriptionMedication::class);
    }

    /**
     * Get the prescriptions that include this medication.
     */
    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'prescription_medications')
                    ->using(PrescriptionMedication::class) // Optional: if you want to access pivot model instance
                    ->withPivot(['dosage', 'frequency', 'duration', 'quantity_prescribed', 'instructions', 'created_by', 'updated_by', 'deleted_by'])
                    ->withTimestamps();
    }
}