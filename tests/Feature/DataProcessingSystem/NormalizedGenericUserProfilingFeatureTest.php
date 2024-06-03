<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Klamo\ProfilingSystem\Events\AttributeWasCreated;
use Klamo\ProfilingSystem\Events\AttributeWasDeleted;
use Klamo\ProfilingSystem\Events\NewGenericUserProfile;
use Klamo\ProfilingSystem\Events\UserAction;
use Klamo\ProfilingSystem\Events\UserWasCreated;
use Klamo\ProfilingSystem\Models\ItemProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\GenericUserProfile;
use Klamo\ProfilingSystem\Models\NormalizedGenericUserProfile;
use Klamo\ProfilingSystem\Models\NormalizedUserProfile;
use Klamo\ProfilingSystem\Models\UserProfile;
use Klamo\ProfilingSystem\Tests\TestCase;


class NormalizedGenericUserProfilingFeatureTest extends TestCase{

    use RefreshDatabase;

    /**
     * Assert that a normalized generic user profile is created, when a generic user profile is created
     */
    public function test_normalized_generic_user_profile_created_when_generic_user_profile_is_created(){
        $this->withoutExceptionHandling();

        //No user profiles exist to begin with
        $this->assertCount(0, GenericUserProfile::all());

        //No normalized user profiles exist to begin with
        $this->assertCount(0, NormalizedGenericUserProfile::all());

        //5 Profiling tags were created
        ProfilingTag::factory(5)->create();

        //A generic user profile is created
        $month = now()->month;
        $year = now()->year;
        event(new NewGenericUserProfile($month, $year));

        //Count of all profiling tags
        $tag_count = ProfilingTag::count();

        //Assert that a user profile has been created
        $this->assertCount(1, GenericUserProfile::all());

        //Assert that a normalized user profile has been created
        $this->assertCount(1, NormalizedGenericUserProfile::all());

        //Grab the first user profile
        $generic_user_profile = GenericUserProfile::with('profilingTags')->first();

        //Grab the first normalized user profile
        $normalized_generic_user_profile = NormalizedGenericUserProfile::with('profilingTags')->first();

        //Assert that profile has correct month and year values
        $this->assertEquals($month, $generic_user_profile->month);
        $this->assertEquals($year, $generic_user_profile->year);

        //Assert that normalized generic user profile and generic user profile have a 1-1 relationship
        $this->assertInstanceOf('Klamo\ProfilingSystem\Models\GenericUserProfile', $normalized_generic_user_profile->genericUserProfile);
        $this->assertInstanceOf('Klamo\ProfilingSystem\Models\NormalizedGenericUserProfile', $generic_user_profile->normalizedGenericUserProfile);

        //Assert that generic user profile has all possible profiling tags
        $this->assertEquals($tag_count, $generic_user_profile->profilingTags()->count());

        //Assert that normalized user profile has all possible profiling tags
        $this->assertEquals($tag_count, $normalized_generic_user_profile->profilingTags()->count());
    }

    /**
     * Assert that a normalized generic user profile is updated with an added tag
     */
    public function test_normalized_generic_user_profile_updated_with_added_tag(){
        $this->withoutExceptionHandling();

        $normalized_generic_user_profile = NormalizedGenericUserProfile::factory()->create();

        //The product was updated with an attribute with the following name
        $name = "blue";
        //Of the following type
        $type = "color";
        
        event(new AttributeWasCreated($name, $type));

        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTag::all());

        //Assert relationship between
        $normalized_generic_user_profile->refresh();
        $profiling_tag = ProfilingTag::first();

        $this->assertTrue($normalized_generic_user_profile->profilingTags->contains($profiling_tag));
    }

    /**
     * Assert that a normalized generic user profile is updated with a removed tag
     */
    public function test_normalized_generic_user_profile_updated_with_removed_tag(){
        $this->withoutExceptionHandling();

        //The product was updated by removing an attribute with the following name
        $name = "blue";
        //Of the following type
        $type = "color";

        $normalized_generic_user_profile = NormalizedGenericUserProfile::factory()->create();

        $profiling_tag = ProfilingTag::create([
            'name' => $name,
            'type' => $type,
        ]);

        $normalized_generic_user_profile->profilingTags()->attach($profiling_tag);
        //Assert that relationship exists
        $this->assertTrue($normalized_generic_user_profile->profilingTags->contains($profiling_tag));

        event(new AttributeWasDeleted($name, $type));

        //Assert that a profiling tag was deleted
        $this->assertCount(0, ProfilingTag::all());

        //Assert relationship between

        $normalized_generic_user_profile->refresh();
        $this->assertFalse($normalized_generic_user_profile->profilingTags->contains($profiling_tag));
    }

    /**
     * Assert that when updating the values of a generic user profile, then the normalized values are calculated 
     */
    public function test_updated_values_on_normalized_user_profile_when_user_profile_tag_values_are_updated()
    {
        $this->withoutExceptionHandling();

        //Assert that no profiling tags, item profiles, user profiles and normalized user profiles, generic user profiles and normalized generic user profiles exist
        $this->assertCount(0, ProfilingTag::all());
        $this->assertCount(0, ItemProfile::all());
        $this->assertCount(0, UserProfile::all());
        $this->assertCount(0, NormalizedUserProfile::all());
        $this->assertCount(0, GenericUserProfile::all());
        $this->assertCount(0, NormalizedGenericUserProfile::all());

        //Create 2 Items with 1 profiling tag each
        ItemProfile::factory(2)->hasProfilingTags()->create();
        
        //Assert that item profiles were created
        $this->assertCount(2, ItemProfile::all());

        //Assert that profiling tags were created
        $this->assertCount(2, ProfilingTag::all());

        //Create a user profile with a normalized profile from a user id
        $user_id = 1;
        event(new UserWasCreated($user_id));

        //Assert that a user profile and a normalized profile were created
        $this->assertCount(1, UserProfile::all());
        $this->assertCount(1, NormalizedUserProfile::all());

        //Create a generic user profile
        $month = now()->month;
        $year = now()->year;
        event(new NewGenericUserProfile($month, $year));

        //Assert that a generic user profile and a normalized generic profile were created
        $this->assertCount(1, GenericUserProfile::all());
        $this->assertCount(1, NormalizedGenericUserProfile::all());

        //Update the values of the user profile with a user action event
        //Note that action VIEWED is worth 5 points
        //Note that action ADDED_TO_CART is worth 10 points
        $action_one = "VIEWED";
        $action_two = "ADDED_TO_CART";

        $item_one_id = ItemProfile::find(1)->id;
        $item_two_id = ItemProfile::find(2)->id;

        event(new UserAction($user_id, $item_one_id, $action_one));
        
        event(new UserAction($user_id, $item_two_id, $action_two));

        //Grab the generic user profile and normalized generic user profile
        $generic_user_profile = GenericUserProfile::first();
        $normalized_generic_user_profile = NormalizedGenericUserProfile::first();

        //Assert the proper points were attached to the values of the tags for the user profile
        $this->assertEquals(5, $generic_user_profile->profilingTags()->find(1)->pivot->value);
        $this->assertEquals(10, $generic_user_profile->profilingTags()->find(2)->pivot->value);

        //Assert the proper weight were attached to the values of the tags for the normalized user profile
        //Note that the total points are 15, therefore
        //A weight of 5/15 = 0.333, in integer value = 33
        //A weight of 10/15 = 0.666, in integer value = 66
        $this->assertEquals(33, $normalized_generic_user_profile->profilingTags()->find(1)->pivot->value);
        $this->assertEquals(66, $normalized_generic_user_profile->profilingTags()->find(2)->pivot->value);
    }
}