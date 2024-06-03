<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Klamo\ProfilingSystem\Events\AttributeWasCreated;
use Klamo\ProfilingSystem\Events\AttributeWasDeleted;
use Klamo\ProfilingSystem\Events\NewGenericUserProfile;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\GenericUserProfile;
use Klamo\ProfilingSystem\Tests\TestCase;


class GenericUserProfilingFeatureTest extends TestCase{

    use RefreshDatabase;

    /**
     * Assert that a generic consumer profile can be created, when the event new generic profile is emitted
     */
    public function test_generic_consumer_profile_created_when_event_new_generic_profile_emitted(){
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, GenericConsumerProfile::all());

        //An Genericconsumer was created with following id
        $generic_consumer_profile_month = now()->month;
        $generic_consumer_profile_year = now()->year; 
        
        ProfilingTag::factory(3)->create();

        //Create a new generic consumer profile using KlamoProfiler
        KlamoProfiler::setup()->genericConsumerProfile()->create(month: $generic_consumer_profile_month, year: $generic_consumer_profile_year);
        //Count of all profiling tags
        $tag_count = ProfilingTag::count();

        //Assert that a profile has been created
        $this->assertCount(1, GenericConsumerProfile::all());

        //Grab the first Genericconsumer profile
        $generic_consumer_profile = GenericConsumerProfile::with('profilingTags')->first();

        //Assert that profile has correct generic consumer month
        $this->assertEquals($generic_consumer_profile_month, $generic_consumer_profile->month);
        //Assert that profile has correct generic consumer year
        $this->assertEquals($generic_consumer_profile_year, $generic_consumer_profile->year);
        //Assert that profile has all possible profiling tags
        $this->assertEquals($tag_count, $generic_consumer_profile->profilingTags()->count());
    }

    /**
     * Assert that an generic user profile is updated with an added tag
     */
    public function test_generic_user_profile_updated_with_added_tag(){
        $this->withoutExceptionHandling();

        $generic_user_profile = GenericConsumerProfile::factory()->create();

        //The product was updated with an attribute with the following name
        $name = 'blue';
        //Of the following type
        $profiling_tag_type = 'color';
        
        KlamoProfiler::setup()->profilingTag()->create(name: $name, profiling_tag_type: $profiling_tag_type);
        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTag::all());

        //Assert relationship between

        $generic_user_profile->refresh();
        $profiling_tag = ProfilingTag::first();

        $this->assertTrue($generic_user_profile->profilingTags->contains($profiling_tag));
    }

    /**
     * Assert that an generic user profile is updated with a removed tag
     */
    public function test_generic_user_profile_updated_with_removed_tag(){
        $this->withoutExceptionHandling();

        //The product was updated by removing an attribute with the following name
        $name = "blue";
        //Of the following type id
        $profiling_tag_type_id = 1;

        $generic_user_profile = GenericConsumerProfile::factory()->create();

        $profiling_tag = ProfilingTag::create([
            'name' => $name,
            'profiling_tag_type_id' => $profiling_tag_type_id,
        ]);

        $generic_user_profile->profilingTags()->attach($profiling_tag);
        //Assert that relationship exists
        $this->assertTrue($generic_user_profile->profilingTags->contains($profiling_tag));

        //Delete profiling tag using klamo profiler
        KlamoProfiler::setup()->profilingTag()->delete($profiling_tag->id);
        //Assert that a profiling tag was deleted
        $this->assertCount(0, ProfilingTag::all());

        //Assert relationship between

        $generic_user_profile->refresh();
        $this->assertFalse($generic_user_profile->profilingTags->contains($profiling_tag));
    }
}