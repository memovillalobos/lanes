<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    //
    protected $fillable = [
        'student_number',
        'name',
        'description',
        'status',
        'hidden_at'
    ];

    protected $dates = [
        'hidden_at',
        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->whereNull('hidden_at');
        });
    }
}
