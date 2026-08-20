<?php

namespace App\Http\Resources;

use App\Models\ReverbApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ReverbApplication */
class ApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'app_id' => $this->app_id,
            'key' => $this->key,
            'allowed_origins' => $this->allowed_origins,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function withSecret(ReverbApplication $application, string $plainSecret): array
    {
        return array_merge(
            (new self($application))->resolve(),
            ['secret' => $plainSecret],
        );
    }
}
