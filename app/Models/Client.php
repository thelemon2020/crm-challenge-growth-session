<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('status','active');
    }

    #[Scope]
    protected function inactive(Builder $query):void
    {
        $query->where('status','inactive');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => date('m/d/Y', strtotime($value)),
        );
    }
}
