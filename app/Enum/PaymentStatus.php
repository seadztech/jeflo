<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PARTIAL => 'Partial',
            self::PAID => 'Paid',
            self::OVERDUE => 'Overdue',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::PARTIAL => 'blue',
            self::PAID => 'green',
            self::OVERDUE => 'red',
        };
    }
    
    public function badge(): string
    {
        $color = $this->color();
        return "<span class='px-2 py-1 rounded-full text-xs bg-{$color}-100 text-{$color}-800'>{$this->label()}</span>";
    }
}