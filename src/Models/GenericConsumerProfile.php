<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenericConsumerProfile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Klamo\ProfilingSystem\Database\Factories\GenericConsumerProfileFactory::new();
    }
      
    public function profilingTags()
    {
        return $this->belongsToMany(ProfilingTag::class, 'generic_consumer_profile_profiling_tag', 'generic_consumer_profile_id','generic_consumer_profiling_tag_id')->withPivot('actions','points','weight');
    }

    public function productProfiles()
    {
        return $this->belongsToMany(ProductProfile::class, 'generic_consumer_profile_product_profile', 'generic_consumer_profile_id','generic_consumer_product_profile_id')->withPivot('preference');
    }
}
