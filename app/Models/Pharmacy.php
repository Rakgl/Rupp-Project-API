<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Import HasUuids

class Pharmacy extends Model
{
    use HasFactory, SoftDeletes, HasUuids; // Add HasUuids trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'phone_number',
        'email',
        'license_number',
        'opening_time',
        'closing_time',
        'is_24_hours',
        'delivers_medication',
        'delivery_details',
        'status',
        'created_by', // Included as per migration, can be handled by observers/traits too
        'updated_by', // Included as per migration
        'deleted_by', // Included as per migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_24_hours' => 'boolean',
        'delivers_medication' => 'boolean',
        'opening_time' => 'datetime:H:i:s', // Or 'datetime:H:i' if seconds are not needed
        'closing_time' => 'datetime:H:i:s', // Or 'datetime:H:i'
        'email_verified_at' => 'datetime', // Example if you add email verification
    ];

    /**
     * The attributes that should be mutated to dates.
     *
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
}