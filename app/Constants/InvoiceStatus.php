<?php

declare(strict_types=1);

namespace App\Constants;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Terkirim',
            self::VIEWED => 'Dilihat',
            self::PARTIAL => 'Dibayar Sebagian',
            self::PAID => 'Lunas',
            self::OVERDUE => 'Jatuh Tempo',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::VIEWED => 'indigo',
            self::PARTIAL => 'yellow',
            self::PAID => 'green',
            self::OVERDUE => 'red',
            self::CANCELLED => 'gray',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::SENT], true);
    }

    public function isPayable(): bool
    {
        return in_array($this, [self::SENT, self::VIEWED, self::PARTIAL, self::OVERDUE], true);
    }
}
