<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Support\Facades\Event;
use Klamo\ProfilingSystem\Events\AttributeWasCreated;
use Klamo\ProfilingSystem\Events\AttributeWasDeleted;
use Klamo\ProfilingSystem\Events\AttributeWasUpdated;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;


class ProfilingTagFeatureTest extends TestCase{

    /**
     * Best case scenario
     * Assert that a profiling tag is created, when an attribute is created
     */
    public function test_tag_created_on_attribute_created_event_test(){
        $this->withoutExceptionHandling();
        
        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());

        //An attribute was created with the following name
        $name = 'blue';
        //Of the following type 
        $type = 'color';
        
        event(new AttributeWasCreated($name, $type));

        //Assert that a profiling tag has been created
        $this->assertCount(1, ProfilingTag::all());

        //Grab the first profiling tag
        $profiling_tag = ProfilingTag::first();
        $this->assertEquals("blue", $profiling_tag->name);
        $this->assertEquals('color', $profiling_tag->type);
    }

    /**
     * Normal case scenario
     * Assert that a profiling tag isn't created, when an attribute is created with non-compatible tag type
     */
    public function test_tag_not_created_on_attribute_created_with_non_compatible_tag_type_event_test(){
        $this->withoutExceptionHandling();
        
        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());

        //An attribute was created with the following name
        $name = "blue";
        //Of the following non-compatible type
        $type = "random";
        
        event(new AttributeWasCreated($name, $type));

        $this->assertCount(0, ProfilingTag::all());
    }

    /**
     * Worst case scenario
     * Assert that a profiling tag isn't created, when an attribute without information is created
     */
    public function test_tag_not_created_on_attribute_created_with_no_information_event_test(){
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());

        //An attribute was created with the following name
        $name = "";
        //Of the following non-compatible type
        $type = "";
        
        event(new AttributeWasCreated($name, $type));

        //No profiling tags created
        $this->assertCount(0, ProfilingTag::all());
    }

    /**
     * Test that a tag gets updated when an attribute gets updated
     */
    public function test_profiling_tag_is_updated_when_attribute_is_updated()
    {
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());
        
        //An attribute was created with the following name
        $name = 'blue';
        //Of the following type 
        $type = 'color';
        //With type id
        $type_id = 1;

        //Create a profiling tag
        $profiling_tag = ProfilingTag::create([
            'name' => $name,
            'type_id' => $type_id,
        ]);  
        
        //Updated attribute has new name
        $new_name = 'large';
        //And new type
        $new_type = 'size';

        //A profiling tag now exists
        $this->assertCount(1, ProfilingTag::all());
        //Delete the profiling tag
        event(new AttributeWasUpdated($name, $type, $new_name, $new_type));
        
        //Assert that profiling tag still exist now
        $this->assertCount(1, ProfilingTag::all());

        //Refresh data from database
        $profiling_tag->refresh();
            
        //Assert that the correct data were updated
        $this->assertEquals('large', $profiling_tag->name);
        $this->assertEquals('size', $profiling_tag->type);
    }


    /**
     * Test that a tag gets deleted when an attribute gets deleted
     */
    public function test_profiling_tag_is_deleted_when_attribute_is_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());
        
        //An attribute was created with the following name
        $name = 'blue';
        //Of the following type 
        $type = 'color';
        //With type id
        $type_id = 1;

        //Create a profiling tag
        $profiling_tag = ProfilingTag::create([
            'name' => $name,
            'type_id' => $type_id,
        ]);    

        //A profiling tag now exists
        $this->assertCount(1, ProfilingTag::all());
        //Delete the profiling tag
        event(new AttributeWasDeleted($name, $type));
        
        //Assert that no profiling tags exist now
        $this->assertCount(0, ProfilingTag::all());
    }

}