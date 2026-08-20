<?php

namespace App\Enums;

enum HubApiKeyPreset: string
{
    case Basic = 'basic';
    case Read = 'read';
    case Manage = 'manage';

    /**
     * @return list<string>
     */
    public function abilities(): array
    {
        return match ($this) {
            self::Basic => ['docs:read', 'applications:create'],
            self::Read => ['docs:read', 'applications:create', 'applications:read'],
            self::Manage => [
                'docs:read',
                'applications:create',
                'applications:read',
                'applications:update',
                'applications:delete',
            ],
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Basic => __('Basic'),
            self::Read => __('Read'),
            self::Manage => __('Manage'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Basic => __('Create Reverb applications via API.'),
            self::Read => __('Basic, plus list applications.'),
            self::Manage => __('Read, plus update, rotate, and delete applications.'),
        };
    }
}
