<?php

namespace Klamo\ProfilingSystem\Repositories\ProfilingTagType;

use Klamo\ProfilingSystem\Jobs\GenerateProfilingTagTypes;
use Klamo\ProfilingSystem\Models\ProfilingTagType;

class ProfilingTagTypeRepository implements IProfilingTagTypeRepository{

    /**
     * Generate
     * 
     * Generates profiling tag types
     * 
     * The generation is passed to a jobs.
     * Returns the job object.
     * 
     * @return Klamo\ProfilingSystem\Jobs\GenerateProfilingTagTypes
     */
    public function generate()
    {
        return new GenerateProfilingTagTypes();
    }

    /**
     * Create
     * 
     * Given a profiling tag type name based on a product attribute
     * Create if it doesn't exist or update the existing one.
     * Return the profiling tag type id
     * 
     * @param profiling_tag_type_name
     * 
     * @return Klamo\ProfilingSystem\Models\ProfilingTagType
     */
    public function create(?String $profiling_tag_type_name)
    {
        //If profiling tag type name is not null, then create a profiling tag type and return it
        if($profiling_tag_type_name){
            return ProfilingTagType::updateOrCreate([
                'name' => $profiling_tag_type_name
            ]);
        }
        return null;
    }

    /**
     * Read
     * 
     * Given a name, return the profiling tag type
     * 
     * @param profiling_tag_type_name
     * 
     * @return \Klamo\ProfilingSystem\Models\ProfilingTagType
     */
    public function read(?String $profiling_tag_type_name)
    {
        //If profiling tag type name is not null, then find its profiling tag type and return it
        if($profiling_tag_type_name) {
            return ProfilingTagType::where('name', $profiling_tag_type_name)->first();
        } 
        return null;
    }

    /**
     * UpdateWeight
     * 
     * Given a profiling tag type name and a value
     * Update the profiling tag type weight with the given value
     * Returns a boolean with the update result
     * 
     * @param profiling_tag_type_name
     * @param weight
     * 
     * @return boolean
     */
    public function updateWeight(?String $profiling_tag_type_name, ?Int $weight)
    {
        $profiling_tag_type = $this->read($profiling_tag_type_name);

        if($profiling_tag_type && $weight){
            return $profiling_tag_type->update([
                'weight' => $weight,
            ]);
        }
        return false;
    }

    /**
     * Delete
     * 
     * Given a profiling tag type name, delete its profiling tag type
     * 
     * @param profiling_tag_type_name
     * 
     * @return boolean
     */
    public function delete(?String $profiling_tag_type_name)
    {
        //Grab profiling tag type based on profiling tag type name and return false if it doesn't exist
        $profiling_tag_type = $this->read($profiling_tag_type_name);
        if($profiling_tag_type){
            //Attempt to delete the consumer profile and return the result
            return $profiling_tag_type->delete();
        }
        return false;
    }        
}