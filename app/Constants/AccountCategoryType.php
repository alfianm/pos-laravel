<?php

namespace App\Constants;

enum AccountCategoryType: int
{
    case ASSET = 1;
    case LIABILITY = 2;
    case EQUITY = 3;
    case REVENUE = 4;
    case EXPENSE = 5;

    public function label(): string
    {
        return match ($this) {
            self::ASSET => 'Aset',
            self::LIABILITY => 'Kewajiban',
            self::EQUITY => 'Ekuitas',
            self::REVENUE => 'Pendapatan',
            self::EXPENSE => 'Beban',
        };
    }

    public function englishLabel(): string
    {
        return match ($this) {
            self::ASSET => 'Asset',
            self::LIABILITY => 'Liability',
            self::EQUITY => 'Equity',
            self::REVENUE => 'Revenue',
            self::EXPENSE => 'Expense',
        };
    }

    public function code(): string
    {
        return match ($this) {
            self::ASSET => '1',
            self::LIABILITY => '2',
            self::EQUITY => '3',
            self::REVENUE => '4',
            self::EXPENSE => '5',
        };
    }

    public function normalBalance(): NormalBalance
    {
        return match ($this) {
            self::ASSET, self::EXPENSE => NormalBalance::DEBIT,
            self::LIABILITY, self::EQUITY, self::REVENUE => NormalBalance::CREDIT,
        };
    }

    public function isAsset(): bool
    {
        return $this === self::ASSET;
    }

    public function isLiability(): bool
    {
        return $this === self::LIABILITY;
    }

    public function isEquity(): bool
    {
        return $this === self::EQUITY;
    }

    public function isRevenue(): bool
    {
        return $this === self::REVENUE;
    }

    public function isExpense(): bool
    {
        return $this === self::EXPENSE;
    }

    public static function options(): array
    {
        return [
            ['value' => self::ASSET->value, 'label' => self::ASSET->label(), 'code' => self::ASSET->code()],
            ['value' => self::LIABILITY->value, 'label' => self::LIABILITY->label(), 'code' => self::LIABILITY->code()],
            ['value' => self::EQUITY->value, 'label' => self::EQUITY->label(), 'code' => self::EQUITY->code()],
            ['value' => self::REVENUE->value, 'label' => self::REVENUE->label(), 'code' => self::REVENUE->code()],
            ['value' => self::EXPENSE->value, 'label' => self::EXPENSE->label(), 'code' => self::EXPENSE->code()],
        ];
    }

    public static function fromCode(string $code): ?self
    {
        return match ($code) {
            '1' => self::ASSET,
            '2' => self::LIABILITY,
            '3' => self::EQUITY,
            '4' => self::REVENUE,
            '5' => self::EXPENSE,
            default => null,
        };
    }
}
