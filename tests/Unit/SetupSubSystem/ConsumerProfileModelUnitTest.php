<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Tests\TestCase;


class ConsumerProfileModelUnitTest extends TestCase{

    use RefreshDatabase;
    /**
     * Assert that the consumer profiles table exists
     */
    public function test_consumer_profiles_table_exists_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasTable('consumer_profiles');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the consumer profiles table has column consumer id
     */
    public function test_consumer_profile_table_has_column_consumer_id()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('consumer_profiles', 'consumer_id');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that an consumer profile can be created
     */
    public function test_consumer_profile_can_be_created()
    {
        $this->withoutExceptionHandling();

        //No consumer profiles exist to begin with
        $this->assertCount(0, ConsumerProfile::all());

        ConsumerProfile::create([
            'consumer_id' => 1,
        ]);

        //Assert creation
        $this->assertCount(1, ConsumerProfile::all());
        //Grab first consumer profile
        $consumer_profile = ConsumerProfile::first();
        //Assert correct values
        $this->assertEquals(1, $consumer_profile->consumer_id);
    }

    /**
     * Assert that an consumer profile can be updated with tags
     */
    public function test_consumer_profile_can_be_updated_with_tags()
    {
        $this->withoutExceptionHandling();

        // Create a new consumer profile
        $consumer_profile = ConsumerProfile::factory()->create();

        // Create a new profiling tag
        $profiling_tag = ProfilingTag::factory()->create();

        // Assert relationship is setup properly
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $consumer_profile->profilingTags);

        // Set relationship between the two
        $consumer_profile->profilingTags()->attach($profiling_tag->id);
        
        // Assert that only a single profiling tag is linked and it's the proper one
        $this->assertEquals($profiling_tag->id, $consumer_profile->profilingTags()->first()->id);
        $this->assertEquals(1, $consumer_profile->profilingTags()->count());

        // Remove the relationship
        $consumer_profile->profilingTags()->detach($profiling_tag->id);
        
        // Assert that link between the consumer profile and the profiling tag is removed
        $this->assertEquals(0, $consumer_profile->profilingTags()->count());
    }

    /**
     * Assert that an consumer profile can be deleted
     */
    public function test_consumer_profile_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling consumers exist to begin with
        $this->assertCount(0, ConsumerProfile::all());

        //Create a profiling consumer
        $consumer_profile = ConsumerProfile::create([
            'consumer_id' => 1,
        ]);

        $consumer_profile_id = $consumer_profile->id;

        $consumer_profile->delete();

        $this->assertDatabaseMissing('consumer_profiles', [
            'id' => $consumer_profile_id
        ]);
    }
}