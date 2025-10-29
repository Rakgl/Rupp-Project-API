<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreNotificationSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'store_notification_settings';

    protected $fillable = [
        'store_id',
        'provider',
        'name',
        'credentials',
        'is_active',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'credentials.smtp_password',
        'credentials.bot_token',
    ];

    /**
     * Supported notification providers.
     */
    const PROVIDERS = [
        'telegram' => 'Telegram',
        'email' => 'Email',
    ];

    /**
     * Required fields for each provider.
     */
    const REQUIRED_CREDENTIALS = [
        'telegram' => ['bot_token', 'chat_id'],
        'email' => ['email', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'from_email'],
    ];


    /**
     * Get the store that owns this notification setting.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }


    /**
     * Check if this notification setting is for Telegram.
     */
    public function isTelegram(): bool
    {
        return $this->provider === 'telegram';
    }

    /**
     * Check if this notification setting is for Email.
     */
    public function isEmail(): bool
    {
        return $this->provider === 'email';
    }

    /**
     * Get the required credentials for the current provider.
     */
    public function getRequiredCredentials(): array
    {
        return self::REQUIRED_CREDENTIALS[$this->provider] ?? [];
    }

    /**
     * Check if all required credentials are present.
     */
    public function hasValidCredentials(): bool
    {
        $required = $this->getRequiredCredentials();
        $credentials = $this->credentials ?? [];

        foreach ($required as $field) {
            if (empty($credentials[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get a masked version of sensitive credentials for display.
     */
    public function getMaskedCredentials(): array
    {
        $credentials = $this->credentials ?? [];

        // Mask sensitive fields
        if (isset($credentials['smtp_password'])) {
            $credentials['smtp_password'] = '********';
        }
        if (isset($credentials['bot_token'])) {
            $credentials['bot_token'] = substr($credentials['bot_token'], 0, 10) . '...';
        }

        return $credentials;
    }

    /**
     * Scope to get only active notification settings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get settings by provider.
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }
}