<?php

namespace App\Constants;

class Subscription
{
    // Subscription status
    public const STATUS_ACTIVE = 'active';
    public const STATUS_GRACE_PERIOD = 'grace_period';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_TRIAL = 'trial';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_GRACE_PERIOD,
        self::STATUS_EXPIRED,
        self::STATUS_CANCELLED,
        self::STATUS_TRIAL,
    ];

    // Plan codes
    public const PLAN_FREE = 'free';
    public const PLAN_STARTER = 'starter';
    public const PLAN_PRO = 'pro';
    public const PLAN_ENTERPRISE = 'enterprise';

    public const PLANS = [
        self::PLAN_FREE,
        self::PLAN_STARTER,
        self::PLAN_PRO,
        self::PLAN_ENTERPRISE,
    ];

    // Billing cycles
    public const BILLING_MONTHLY = 'monthly';
    public const BILLING_YEARLY = 'yearly';

    public const BILLING_CYCLES = [
        self::BILLING_MONTHLY,
        self::BILLING_YEARLY,
    ];

    // Invoice status
    public const INVOICE_STATUS_DRAFT = 'draft';
    public const INVOICE_STATUS_SENT = 'sent';
    public const INVOICE_STATUS_PAID = 'paid';
    public const INVOICE_STATUS_OVERDUE = 'overdue';
    public const INVOICE_STATUS_CANCELLED = 'cancelled';

    public const INVOICE_STATUSES = [
        self::INVOICE_STATUS_DRAFT,
        self::INVOICE_STATUS_SENT,
        self::INVOICE_STATUS_PAID,
        self::INVOICE_STATUS_OVERDUE,
        self::INVOICE_STATUS_CANCELLED,
    ];

    // Payment status
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_PENDING,
        self::PAYMENT_STATUS_PAID,
        self::PAYMENT_STATUS_FAILED,
        self::PAYMENT_STATUS_REFUNDED,
    ];

    // Payment types
    public const PAYMENT_TYPE_SUBSCRIPTION = 'subscription';
    public const PAYMENT_TYPE_INVOICE = 'invoice';
    public const PAYMENT_TYPE_TOPUP = 'topup';

    public const PAYMENT_TYPES = [
        self::PAYMENT_TYPE_SUBSCRIPTION,
        self::PAYMENT_TYPE_INVOICE,
        self::PAYMENT_TYPE_TOPUP,
    ];

    // Quota types
    public const QUOTA_BRANCHES = 'branches';
    public const QUOTA_PRODUCTS = 'products';
    public const QUOTA_USERS = 'users';
    public const QUOTA_TRANSACTIONS = 'transactions';
    public const QUOTA_STORAGE = 'storage';

    public const QUOTA_TYPES = [
        self::QUOTA_BRANCHES,
        self::QUOTA_PRODUCTS,
        self::QUOTA_USERS,
        self::QUOTA_TRANSACTIONS,
        self::QUOTA_STORAGE,
    ];
}
