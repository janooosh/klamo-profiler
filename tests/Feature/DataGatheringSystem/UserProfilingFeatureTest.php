<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Klamo\ProfilingSystem\Events\AttributeWasCreated;
use Klamo\ProfilingSystem\Events\AttributeWasDeleted;
use Klamo\ProfilingSystem\Events\UserWasCreated;
use Klamo\ProfilingSystem\Events\UserWasDeleted;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\UserProfile;
use Klamo\ProfilingSystem\Tests\TestCase;


class UserProfilingFeatureTest extends TestCase{

    use RefreshDatabase;

    /**
     * Assert that a user profile is created, when a user is created
     */
    public function test_user_profile_created_on_user_created_event_test(){
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, UserProfile::all());

        //An user was created with following id
        $user_id = 1;
        
        ProfilingTag::create([
            'name' => 'blue',
            'type_id' => 1,
        ]);

        event(new UserWasCreated($user_id));

        //Count of all profiling tags
        $tag_count = ProfilingTag::count();

        //Assert that a profile has been created
        $this->assertCount(1, UserProfile::all());

        //Grab the first user profile
        $user_profile = UserProfile::with('profilingTags')->first();

        //Assert that profile has correct user id
        $this->assertEquals(1, $user_profile->user_id);
        //Assert that profile has all possible profiling tags
        $this->assertEquals($tag_count, $user_profile->profilingTags()->count());
    }

    /**
     * Assert that an user profile is updated with an added tag
     */
    public function test_user_profile_updated_with_added_tag(){
        $this->withoutExceptionHandling();

        //The product that is updated has an id of
        $user_id = 1;

        $user_profile = UserProfile::create([
            'user_id' => $user_id,
        ]);

        //The product was updated with an attribute with the following name
        $name = "blue";
        //Of the following type
        $type = "color";
        
        event(new AttributeWasCreated($name, $type));

        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTag::all());

        //Assert relationship between

        $user_profile->refresh();
        $profiling_tag = ProfilingTag::first();

        $this->assertTrue($user_profile->profilingTags->contains($profiling_tag));
    }

    /**
     * Assert that an user profile is updated with a removed tag
     */
    public function test_user_profile_updated_with_removed_tag(){
        $this->withoutExceptionHandling();

        //The product that is updated has an id of
        $user_id = 1;

        //The product was updated by removing an attribute with the following name
        $name = "blue";
        //Of the following type
        $type = "color";

        $user_profile = userProfile::create([
            'user_id' => $user_id,
        ]);

        $profiling_tag = ProfilingTag::create([
            'name' => $name,
            'type' => $type,
        ]);

        $user_profile->profilingTags()->attach($profiling_tag);
        //Assert that relationship exists
        $this->assertTrue($user_profile->profilingTags->contains($profiling_tag));

        event(new AttributeWasDeleted($name, $type));

        //Assert that a profiling tag was deleted
        $this->assertCount(0, ProfilingTag::all());

        //Assert relationship between

        $user_profile->refresh();
        $this->assertFalse($user_profile->profilingTags->contains($profiling_tag));
    }

    /**
     * Assert that an user profile is deleted, when an user is deleted
     */
    public function test_user_profile_deleted_on_user_deleted_event_test(){
        $this->withoutExceptionHandling();

        //Create a user profile with a normalized user profile
        $user_id = 1;
        UserProfile::factory(1)->hasNormalizedUserProfile()->create([
            'user_id' => $user_id,
        ]);
        
        event(new UserWasDeleted($user_id));

        //No user profiles exist to
        $this->assertCount(0, UserProfile::all());
    }
}