<?php 

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH = 'cash';
    case MPESA = 'mpesa';
    case CREDIT = 'credit';
    case CARD = 'card';
    case BANK_TRANSFER = 'bank_transfer';
    
    public function label(): string
    {
        return match($this) {
            self::CASH => 'Cash',
            self::MPESA => 'M-PESA',
            self::CREDIT => 'Credit',
            self::CARD => 'Card',
            self::BANK_TRANSFER => 'Bank Transfer',
        };
    }
    
    public function requiresImmediatePayment(): bool
    {
        return $this !== self::CREDIT;
    }
    
    public function icon(): string
    {
        return match($this) {
            self::CASH => 'fa-money-bill',
            self::MPESA => 'fa-mobile-alt',
            self::CREDIT => 'fa-credit-card',
            self::CARD => 'fa-credit-card',
            self::BANK_TRANSFER => 'fa-university',
        };
    }
}