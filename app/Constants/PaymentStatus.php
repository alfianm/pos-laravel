<?php
declare(strict_types=1);
namespace App\Constants;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::COMPLETED => 'Selesai',
            self::FAILED => 'Gagal',
            self::REFUNDED => 'Dikembalikan',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::COMPLETED => 'green',
            self::FAILED => 'red',
            self::REFUNDED => 'orange',
            self::CANCELLED => 'gray',
        };
    }
}
