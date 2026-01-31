<?php 

namespace App\Enums;

enum TransactionType: string
{
    case CASH = 'cash';
    case MPESA = 'mpesa';
    case CREDIT_SALE = 'credit_sale';
    case CREDIT_PAYMENT = 'credit_payment';
    case CARD = 'card';
    case BANK_TRANSFER = 'bank_transfer';
    
    public function label(): string
    {
        return match($this) {
            self::CASH => 'Cash',
            self::MPESA => 'M-PESA',
            self::CREDIT_SALE => 'Credit Sale',
            self::CREDIT_PAYMENT => 'Credit Payment',
            self::CARD => 'Card',
            self::BANK_TRANSFER => 'Bank Transfer',
        };
    }
    
    public function isPayment(): bool
    {
        return in_array($this, [
            self::CASH,
            self::MPESA,
            self::CREDIT_PAYMENT,
            self::CARD,
            self::BANK_TRANSFER,
        ]);
    }
    
    public function isCredit(): bool
    {
        return in_array($this, [
            self::CREDIT_SALE,
            self::CREDIT_PAYMENT,
        ]);
    }
    
    public function icon(): string
    {
        return match($this) {
            self::CASH => 'fa-money-bill',
            self::MPESA => 'fa-mobile-alt',
            self::CREDIT_SALE => 'fa-credit-card',
            self::CREDIT_PAYMENT => 'fa-money-check-alt',
            self::CARD => 'fa-credit-card',
            self::BANK_TRANSFER => 'fa-university',
        };
    }
}