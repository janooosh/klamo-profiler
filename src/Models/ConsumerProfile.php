<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumerProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Klamo\ProfilingSystem\Database\Factories\ConsumerProfileFactory::new();
    }
      
    public function profilingTags()
    {
        return $this->belongsToMany(ProfilingTag::class, 'consumer_profile_profiling_tag', 'consumer_profile_id','consumer_profiling_tag_id')->withPivot('actions','points','weight');
    }

    public function productProfiles()
    {
        return $this->belongsToMany(ProductProfile::class, 'consumer_profile_product_profile', 'consumer_profile_id', 'consumer_product_profile_id')->withPivot('preference');
    }
}
