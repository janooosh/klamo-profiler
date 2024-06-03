<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerAction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Klamo\ProfilingSystem\Database\Factories\ConsumerActionFactory::new();
    }
}
