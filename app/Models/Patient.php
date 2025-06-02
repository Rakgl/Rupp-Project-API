<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\DataQuery; // Assuming this trait exists
use App\Traits\DataScope;  // Assuming this trait exists
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Patient extends Model
{
    use HasFactory, SoftDeletes, DataQuery, DataScope, HasUuids; 

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'blood_group',
        'address_line_1',
        'city',
        'state',
        'zip_code',
        'country',
        'profile_picture_path',
        'insurance_provider_id',
        'insurance_policy_number',
        'insurance_policy_expiry_date',
        'medical_history_summary',
        'allergies',
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
        'insurance_policy_expiry_date' => 'date',
        'allergies' => 'json', // Assuming you want to store allergies as JSON
    ];

    /**
     * Get the user that owns the patient record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
