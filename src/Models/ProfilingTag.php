<?php

namespace Klamo\ProfilingSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilingTag extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
    * Profiling tag types
    *
    * @var array
    */
    public const TYPES = [
      1 => 'globalColor',
      2 =>'globalSize',
      3 => 'category',
      4 => 'klamoValue',
      5 => 'trending'
  ];

    protected static function newFactory(){
      return \Klamo\ProfilingSystem\Database\Factories\ProfilingTagFactory::new();
    }
    /**
     * Returns the id of a given type
     */
    public static function getTypeID($type)
    {
        return array_search($type, self::TYPES);
    }
    
    /**
     * Get the profiling tag type
     */
    public function getTypeAttribute()
    {
      return self::TYPES[$this->attributes['type_id']];
    }
  
    /**
    * Set profiling tag type
    */
    public function setTypeAttribute($value)
    {
      $type_id = self::getTypeID($value);
      if ($type_id) {
         $this->attributes['type_id'] = $type_id;
      }
    }

    public function profilingTagType()
    {
      return $this->belongsTo(ProfilingTagType::class);
    }

    public function productProfiles()
    {
      return $this->belongsToMany(ProductProfile::class)->withPivot('weight');
    }

    public function consumerProfiles()
    {
      return $this->belongsToMany(ConsumerProfile::class)->withPivot('points','weight');
    }

    public function genericConsumerProfiles()
    {
      return $this->belongsToMany(GenericConsumerProfile::class)->withPivot('points','weight');
    }
}
