<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilingTagType extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Klamo\ProfilingSystem\Database\Factories\ProfilingTagTypeFactory::new();
    }

    public function profilingTags()
    {
        return $this->hasMany(ProfilingTag::class);
    }
}
