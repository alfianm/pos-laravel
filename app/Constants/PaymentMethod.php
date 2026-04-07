<?php
declare(strict_types=1);
namespace App\Constants;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case TRANSFER = 'transfer';
    case CHECK = 'check';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case EWALLET = 'ewallet';
    case QRIS = 'qris';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Tunai',
            self::TRANSFER => 'Transfer Bank',
            self::CHECK => 'Cek',
            self::CREDIT_CARD => 'Kartu Kredit',
            self::DEBIT_CARD => 'Kartu Debit',
            self::EWALLET => 'E-Wallet',
            self::QRIS => 'QRIS',
        };
    }
}
