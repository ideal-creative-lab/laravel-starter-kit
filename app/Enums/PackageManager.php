<?php

declare(strict_types=1);

namespace App\Enums;

enum PackageManager: string
{
    case BUN = 'bun';
    case NPM = 'npm';
    case YARN = 'yarn';

    public static function getValues(): array {
        return array_column(PackageManager::cases(), 'value');
    }

    public static function getRunExecutablesCommand(string $manager): string {
        return match ($manager) {
            PackageManager::BUN->value => 'bunx',
            PackageManager::NPM->value => 'npx',
            PackageManager::YARN->value => 'yarn',
        };
    }
}
