<?php

namespace App\Constants;

class ReturnStatus
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public const STATUSES = [
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
        self::COMPLETED,
        self::CANCELLED,
    ];

    public const REFUND_PENDING = 'pending';
    public const REFUND_PARTIAL = 'partial';
    public const REFUND_COMPLETED = 'completed';
    public const REFUND_CANCELLED = 'cancelled';

    public const REFUND_STATUSES = [
        self::REFUND_PENDING,
        self::REFUND_PARTIAL,
        self::REFUND_COMPLETED,
        self::REFUND_CANCELLED,
    ];
}
