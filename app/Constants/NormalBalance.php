<?php

namespace App\Constants;

enum NormalBalance: int
{
    case DEBIT = 1;
    case CREDIT = -1;

    public function label(): string
    {
        return match ($this) {
            self::DEBIT => 'Debit',
            self::CREDIT => 'Kredit',
        };
    }

    public function englishLabel(): string
    {
        return match ($this) {
            self::DEBIT => 'Debit',
            self::CREDIT => 'Credit',
        };
    }

    public function isDebit(): bool
    {
        return $this === self::DEBIT;
    }

    public function isCredit(): bool
    {
        return $this === self::CREDIT;
    }

    public static function options(): array
    {
        return [
            ['value' => self::DEBIT->value, 'label' => self::DEBIT->label()],
            ['value' => self::CREDIT->value, 'label' => self::CREDIT->label()],
        ];
    }
}
