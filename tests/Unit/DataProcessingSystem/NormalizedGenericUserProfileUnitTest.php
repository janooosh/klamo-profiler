<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\NormalizedGenericUserProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;


class NormalizedGenericUserProfileUnitTest extends TestCase{

    use RefreshDatabase;
    /**
     * Assert that the normalized generic user profiles table exists
     */
    public function test_normalized_generic_user_profiles_table_exists_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasTable('normalized_generic_user_profiles');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the normalized generic user profiles table has column generic user profile id
     */
    public function test_normalized_generic_user_profile_table_has_column_generic_user_profile_id()
    {
        $this->withoutExceptionHandling();
        
        $column_exists  = Schema::hasColumn('normalized_generic_user_profiles', 'generic_user_profile_id');
        $this->assertTrue($column_exists);
    }

    /**
     * Assert that an normalized generic user profile can be created
     */
    public function test_normalized_generic_user_profile_can_be_created()
    {
        $this->withoutExceptionHandling();

        //No normalized generic user profiles exist to begin with
        $this->assertCount(0, NormalizedGenericUserProfile::all());

        $normalized_generic_user_profile_id = 1;

        NormalizedGenericUserProfile::create([
            'generic_user_profile_id' => $normalized_generic_user_profile_id,
        ]);

        //Assert creation
        $this->assertCount(1, NormalizedGenericUserProfile::all());
        //Grab first user profile
        $normalized_generic_user_profile = NormalizedGenericUserProfile::first();
        //Assert correct values for columns
        $this->assertEquals($normalized_generic_user_profile_id, $normalized_generic_user_profile->generic_user_profile_id);
    }

    /**
     * Assert that a normalized generic user profile can be updated with tags
     */
    public function test__normalized_generic_user_profile_can_be_updated_with_tags()
    {
        $this->withoutExceptionHandling();

        $normalized_generic_user_profile = NormalizedGenericUserProfile::factory()->create();

        $profiling_tag = ProfilingTag::factory()->create();

        
        $normalized_generic_user_profile->profilingTags()->attach($profiling_tag->id);
        
        //Assert relationship
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $normalized_generic_user_profile->profilingTags);
        $this->assertInstanceOf('Klamo\ProfilingSystem\Models\ProfilingTag', $normalized_generic_user_profile->profilingTags()->first());

        $this->assertEquals($profiling_tag->id, $normalized_generic_user_profile->profilingTags()->first()->id);
        $this->assertEquals(1, $normalized_generic_user_profile->profilingTags()->count());

        $normalized_generic_user_profile->profilingTags()->detach($profiling_tag->id);
        
        $this->assertEquals(0, $normalized_generic_user_profile->profilingTags()->count());
    }

    /**
     * Assert that an user profile can be deleted
     */
    public function test_user_profile_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling users exist to begin with
        $this->assertCount(0, NormalizedGenericUserProfile::all());

        //Create a profiling user
        $normalized_generic_user_profile = NormalizedGenericUserProfile::factory()->create();

        $normalized_generic_user_profile_id = $normalized_generic_user_profile->id;

        $normalized_generic_user_profile->delete();

        $this->assertDatabaseMissing('normalized_generic_user_profiles', [
            'id' => $normalized_generic_user_profile_id
        ]);
    }
}