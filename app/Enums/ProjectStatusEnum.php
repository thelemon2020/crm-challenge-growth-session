<?php

namespace App\Enums;

enum ProjectStatusEnum: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'pending',
            self::InProgress => 'in_progress',
            self::OnHold => 'on_hold',
            self::Review => 'review',
            self::Completed => 'completed',
            self::Cancelled => 'cancelled',
        };
    }
}
