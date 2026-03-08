<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SecuritySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        $setting = Cache::remember("security_setting_{$key}", 3600, function () use ($key) {
            return self::where('key', $key)->first();
        });

        if (! $setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value.
     */
    public static function set($key, $value, $type = null, $description = null, $group = 'general')
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'type' => $type ?? (is_bool($value) ? 'boolean' : (is_int($value) ? 'integer' : (is_array($value) ? 'json' : 'string'))),
                'description' => $description,
                'group' => $group,
            ]
        );

        Cache::forget("security_setting_{$key}");
        Cache::forget('security_settings_all');

        return $setting;
    }

    /**
     * Get all settings, optionally filtered by group.
     */
    public static function getAllSettings($group = null)
    {
        $cacheKey = $group ? "security_settings_group_{$group}" : 'security_settings_all';

        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $query = self::query();

            if ($group) {
                $query->where('group', $group);
            }

            return $query->get()->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })->toArray();
        });
    }

    /**
     * Get settings grouped by their group with key-value format.
     */
    public static function getGroupedSettings()
    {
        $settings = self::all();
        $grouped = [];

        foreach ($settings as $setting) {
            $group = $setting->group ?? 'general';
            if (! isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $grouped;
    }

    /**
     * Cast value to appropriate type.
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Clear all security settings cache.
     */
    public static function clearCache()
    {
        $settings = self::all();
        foreach ($settings as $setting) {
            Cache::forget("security_setting_{$setting->key}");
        }
        Cache::forget('security_settings_all');

        $groups = self::distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("security_settings_group_{$group}");
        }
    }
}
