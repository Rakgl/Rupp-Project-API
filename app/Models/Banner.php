<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Required for UUID generation if you create models manually
use Illuminate\Database\Eloquent\SoftDeletes; // Import SoftDeletes
use App\Traits\DataQuery; // Assuming this trait exists
use App\Traits\DataScope;  // Assuming this trait exists
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Banner extends Model
{
	use HasFactory, HasUuids, SoftDeletes, DataQuery, DataScope;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banners';

    /**
     * The "type" of the primary key ID.
     * Indicates that the IDs are UUIDs.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * Set to false because we are using UUIDs.
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
        'id', // Allow mass assignment of ID since we're generating UUIDs manually or in seeder
        'name',
        'image_url_mobile',
        'image_url_tablet',
        'title_text',
        'subtitle_text',
        'cta_text',
        'cta_action_type',
        'cta_action_value',
        'priority',
        'status',
        'start_date',
        'end_date',
        'display_locations',
        'language_code',
        'region_code',
        'impression_count',
        'click_count',
        'created_by',
        'updated_by',
		'deleted_at'
    ];


	 /**
     * The attributes that should be mutated to dates.
     * This is for SoftDeletes.
     * @var array
     */
    protected $dates = ['deleted_at'];
	
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'priority' => 'integer',
        'impression_count' => 'integer',
        'click_count' => 'integer',
    ];

    /**
     * Boot function from Laravel.
     * Used here to automatically set the UUID for new Banner records.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the user who created the banner.
     * Assuming you have a User model.
     */
    public function creator()
    {
        // Ensure you have a User model and the foreign key 'created_by' references the 'id' on the users table.
        // If your User model's primary key is also UUID, this relationship should work as is.
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the banner.
     * Assuming you have a User model.
     */
    public function updater()
    {
        // Ensure you have a User model and the foreign key 'updated_by' references the 'id' on the users table.
        return $this->belongsTo(User::class, 'updated_by');
    }
}
