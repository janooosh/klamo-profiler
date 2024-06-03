<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;


class GenericConsumerProfileModelUnitTest extends TestCase{

    use RefreshDatabase;
    /**
     * Assert that the generic consumer profiles table exists
     */
    public function test_generic_consumer_profiles_table_exists_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasTable('generic_consumer_profiles');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the generic consumer profiles table has column month
     */
    public function test_generic_consumer_profile_table_has_column_month()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('generic_consumer_profiles', 'month');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the generic consumer profiles table has column year
     */
    public function test_generic_consumer_profile_table_has_column_year()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('generic_consumer_profiles', 'year');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that a generic consumer profile can be created
     */
    public function test_generic_consumer_profile_can_be_created()
    {
        $this->withoutExceptionHandling();

        //No consumer profiles exist to begin with
        $this->assertCount(0, GenericConsumerProfile::all());

        $generic_consumer_profile_month = now()->month;
        $generic_consumer_profile_year = now()->year; 

        GenericConsumerProfile::create([
            'month' => $generic_consumer_profile_month,
            'year' => $generic_consumer_profile_year,
        ]);

        //Assert creation
        $this->assertCount(1, GenericConsumerProfile::all());
        //Grab first consumer profile
        $generic_consumer_profile = GenericConsumerProfile::first();
        //Assert correct values for columns
        $this->assertEquals($generic_consumer_profile_month, $generic_consumer_profile->month);
        $this->assertEquals($generic_consumer_profile_year, $generic_consumer_profile->year);
    }

    /**
     * Assert that a generic consumer profile can be updated with tags
     */
    public function test_generic_consumer_profile_can_be_updated_with_tags()
    {
        $this->withoutExceptionHandling();

        $generic_consumer_profile = GenericConsumerProfile::factory()->create();

        // Create a new profiling tag
        $profiling_tag = ProfilingTag::factory()->create();

        // Assert relationship is setup properly
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $generic_consumer_profile->profilingTags);

        // Set relationship between the two
        $generic_consumer_profile->profilingTags()->attach($profiling_tag->id);
        
        // Assert that only a single profiling tag is linked and it's the proper one
        $this->assertEquals($profiling_tag->id, $generic_consumer_profile->profilingTags()->first()->id);
        $this->assertEquals(1, $generic_consumer_profile->profilingTags()->count());

        // Remove the relationship
        $generic_consumer_profile->profilingTags()->detach($profiling_tag->id);
        
        // Assert that link between the consumer profile and the profiling tag is removed
        $this->assertEquals(0, $generic_consumer_profile->profilingTags()->count());
    }

    /**
     * Assert that a generic consumer profile can be deleted
     */
    public function test_generic_consumer_profile_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling consumers exist to begin with
        $this->assertCount(0, GenericConsumerProfile::all());

        //Create a profiling consumer
        $generic_consumer_profile = GenericConsumerProfile::factory()->create();

        $generic_consumer_profile_id = $generic_consumer_profile->id;

        $generic_consumer_profile->delete();

        $this->assertDatabaseMissing('generic_consumer_profiles', [
            'id' => $generic_consumer_profile_id
        ]);
    }
}