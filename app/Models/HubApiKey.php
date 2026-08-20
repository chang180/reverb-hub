<?php

namespace App\Models;

use Database\Factories\HubApiKeyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $prefix
 * @property string $token_hash
 * @property list<string> $abilities
 * @property Carbon|null $last_used_at
 * @property Carbon|null $revoked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id',
    'name',
    'prefix',
    'token_hash',
    'abilities',
    'last_used_at',
    'revoked_at',
])]
class HubApiKey extends Model
{
    /** @use HasFactory<HubApiKeyFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'last_used_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasAbility(string $ability): bool
    {
        return in_array($ability, $this->abilities, true);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    /**
     * @return array{plain: string, prefix: string, hash: string}
     */
    public static function generateToken(): array
    {
        $random = Str::lower(Str::random(48));
        $plain = 'rh_'.$random;

        return [
            'plain' => $plain,
            'prefix' => 'rh_'.Str::substr($random, 0, 8),
            'hash' => hash('sha256', $plain),
        ];
    }

    public static function findByPlainToken(string $plain): ?self
    {
        if (! str_starts_with($plain, 'rh_')) {
            return null;
        }

        return static::query()
            ->where('token_hash', hash('sha256', $plain))
            ->whereNull('revoked_at')
            ->first();
    }
}
