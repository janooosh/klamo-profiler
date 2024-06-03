<?php

namespace Klamo\ProfilingSystem\Repositories\ProfilingTag;

use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Jobs\GenerateProfilingTags;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class ProfilingTagRepository implements IProfilingTagRepository{

    /**
     * Generate
     * 
     * Mass generates Profiling Tags up based on:
     *  - A products attribute model
     *  - The name of the profiling tag is derived from the column name
     *  - The type of the profiling tag is passed as the type parameter
     * 
     * The mass generation is passed to a of jobs.
     * Returns the job object.
     * 
     * @param attribute_class
     * @param column_name
     * @param profiling_tag_type
     * 
     * @return Klamo\ProfilingSystem\Jobs\GenerateProfilingTags
     */
    public function generate(String $attribute_class,String $column_name,String $profiling_tag_type_name)
    {
        return new GenerateProfilingTags($attribute_class, $column_name, $profiling_tag_type_name);
    }

    /**
     * Create
     * 
     * Creates a new Profiling Tags up based on:
     *  - The name of the profiling tag is derived from the column name
     *  - The type of the profiling tag is passed as the type parameter
     * 
     * Create if it doesn't exist or update the existing one.
     * Return the profiling tag
     * 
     * @param profiling_tag_name
     * @param profiling_tag_type
     * 
     * @return Klamo\ProfilingSystem\Models\ProfilingTag
     */
    public function create(?String $profiling_tag_name,?String $profiling_tag_type_name)
    {
        //Given profiling tag type name, get the profiling tag type
        $profiling_tag_type = KlamoProfiler::setup()->ProfilingTagType()->read($profiling_tag_type_name);
        
        //If profiling tag name and profiling tag type are not null, then update or create a profiling tag
        if($profiling_tag_name && $profiling_tag_type){
            return ProfilingTag::updateOrCreate([
                        'name' => $profiling_tag_name,
                        'profiling_tag_type_id' => $profiling_tag_type->id
                    ]);
        }
        return null;
    }

    /**
     * Read
     * 
     * Given a profiling tag name and type name, return the profiling tag
     * 
     * @param profiling_tag_name
     * @param profiling_tag_type
     * 
     * @return \Klamo\ProfilingSystem\Models\ProfilingTag
     */
    public function read(?String $profiling_tag_name, ?String $profiling_tag_type_name)
    {
        //Given profiling tag type name, get the profiling tag type
        $profiling_tag_type = KlamoProfiler::setup()->ProfilingTagType()->read($profiling_tag_type_name);

        //If profiling tag name and profiling tag type are not null, then return the profiling tag
        if($profiling_tag_name && $profiling_tag_type) {
            return ProfilingTag::where('name', $profiling_tag_name)->where('profiling_tag_type_id', $profiling_tag_type->id)->first();
        }
        return null;
    }

    /**
     * Delete
     * 
     * Given a profiling tag name and type name, delete the profiling tag
     * 
     * @param profiling_tag_name
     * @param profiling_tag_type
     * 
     * @return boolean
     */
    public function delete(?String $profiling_tag_name, ?String $profiling_tag_type_name)
    {
        //Grab profiling tag based on name and type and return false if it doesn't exist
        $profiling_tag = $this->read($profiling_tag_name, $profiling_tag_type_name);
        if($profiling_tag){
            //Attempt to delete the consumer profile and return the result
            return $profiling_tag->delete();
        }
        return false;
    }

    

}