<?php

namespace App\Models;

use Database\Factories\ReverbApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Reverb\Application;

/**
 * @property int $id
 * @property string $name
 * @property string $app_id
 * @property string $key
 * @property string $secret
 * @property list<string> $allowed_origins
 * @property int|null $max_connections
 * @property int $ping_interval
 * @property int $activity_timeout
 * @property int $max_message_size
 * @property bool $enabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'app_id',
    'key',
    'secret',
    'allowed_origins',
    'max_connections',
    'ping_interval',
    'activity_timeout',
    'max_message_size',
    'enabled',
])]
class ReverbApplication extends Model
{
    /** @use HasFactory<ReverbApplicationFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => self::flushCredentialCache());
        static::deleted(fn () => self::flushCredentialCache());
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'secret' => 'encrypted',
            'allowed_origins' => 'array',
            'enabled' => 'boolean',
            'max_connections' => 'integer',
            'ping_interval' => 'integer',
            'activity_timeout' => 'integer',
            'max_message_size' => 'integer',
        ];
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * @return array{app_id: string, key: string, secret: string}
     */
    public function assignNewCredentials(): array
    {
        $plainSecret = Str::lower(Str::random(32));

        $this->app_id = (string) random_int(100_000, 999_999);
        $this->key = Str::lower(Str::random(20));
        $this->secret = $plainSecret;

        return [
            'app_id' => $this->app_id,
            'key' => $this->key,
            'secret' => $plainSecret,
        ];
    }

    public function toReverbApplication(): Application
    {
        return new Application(
            $this->app_id,
            $this->key,
            $this->secret,
            $this->ping_interval,
            $this->activity_timeout,
            $this->allowed_origins !== [] ? $this->allowed_origins : ['*'],
            $this->max_message_size,
            $this->max_connections,
        );
    }

    public static function flushCredentialCache(): void
    {
        Cache::forget('reverb.apps.all');
    }
}
