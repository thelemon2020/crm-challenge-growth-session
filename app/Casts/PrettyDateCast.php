<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PrettyDateCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes)
    {
        return Carbon::parse($value)->format('m/d/Y');
    }

    public function set(Model $model, string $key, $value, array $attributes)
    {
        return Carbon::parse($value)->toDateString();
    }
}
