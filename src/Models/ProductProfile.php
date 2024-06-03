<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Klamo\ProfilingSystem\Database\Factories\ProductProfileFactory::new();
    }

    public function profilingTags()
    {
        return $this->belongsToMany(ProfilingTag::class, 'product_profile_profiling_tag', 'product_profile_id','product_profiling_tag_id')->withPivot('enabled');
    }

    public function consumerProfiles()
    {
        return $this->belongsToMany(ConsumerProfile::class)->withPivot('preference');
    }
}
