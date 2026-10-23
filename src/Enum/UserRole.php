<?php

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum UserRole: string implements TranslatableInterface
{
    case Free    = 'ROLE_FREE';
    case Premium = 'ROLE_PREMIUM';
    case Admin   = 'ROLE_ADMIN';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->value, [], 'roles', $locale);
    }
}
