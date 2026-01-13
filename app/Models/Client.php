<?php

namespace App\Models;

use App\Casts\PrettyDateCast;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'created_at' => PrettyDateCast::class,
    ];

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

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
