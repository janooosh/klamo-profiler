<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestAttribute extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Klamo\ProfilingSystem\Database\Factories\TestAttributeFactory::new();
    }
}
