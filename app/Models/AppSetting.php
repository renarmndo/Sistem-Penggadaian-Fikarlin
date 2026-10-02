<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;

    public const KEY_INTEREST_RATE = 'default_interest_rate';
    public const KEY_PENALTY_RATE_PER_DAY = 'penalty_rate_per_day';
    public const KEY_DEFAULT_TENOR_DAYS = 'default_tenor_days';
    public const KEY_PLAFON_PERCENTAGE = 'plafon_percentage';

    protected $fillable = [
        'key',
        'value',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key with fallback.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value, ?string $label = null, ?string $description = null): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value' => (string) $value,
                'label' => $label,
                'description' => $description,
            ])
        );
    }
}
