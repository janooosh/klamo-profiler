<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Klamo\ProfilingSystem\Events\NewGenericUserProfile;
use Klamo\ProfilingSystem\Events\UserAction;
use Klamo\ProfilingSystem\Events\UserWasCreated;
use Klamo\ProfilingSystem\Models\ItemProfile;
use Klamo\ProfilingSystem\Models\NormalizedUserProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\UserProfile;
use Klamo\ProfilingSystem\Tests\TestCase;


class ItemRecommendationLoadingTest extends TestCase{

    use RefreshDatabase;

    public function test_item_preference_loading()
    {
        $this->withoutExceptionHandling();

        //Begin preparations
        //First Assert that no profiling tags, item profiles, user profiles and normalized user profiles exist
        $this->assertCount(0, ProfilingTag::all());
        $this->assertCount(0, ItemProfile::all());
        $this->assertCount(0, UserProfile::all());
        $this->assertCount(0, NormalizedUserProfile::all());

        //Create 1000 Item profiles
        ItemProfile::factory(1000)->create();
        //Create 80 Profiling tags
        ProfilingTag::factory(80)->create();
        
        //Assert that 4 item profiles were created
        $this->assertCount(1000, ItemProfile::all());

        //Assert that 80 profiling tags were created
        $this->assertCount(80, ProfilingTag::all());

        //Attach 4 profiling tags to each item profile
        $item_profiles = ItemProfile::all();

        $item_profiles->each(function($item_profile){
            $profiling_tag_ids = [rand(1,20),rand(21,40),rand(41,60),rand(61,80)];
            $item_profile->profilingTags()->sync($profiling_tag_ids);
        });

        //Create a user profile with a normalized profile from a user id
        $user_id = 1;
        event(new UserWasCreated($user_id));
        event(new NewGenericUserProfile('random_month', 'random_year'));
        
        $user_profile = UserProfile::with('itemPreferences')->first();
        //Assert that a user profile and a normalized profile were created
        $this->assertCount(1, UserProfile::all());
        $this->assertCount(1, NormalizedUserProfile::all());

        //Update the values of the user profile with a user action event
        //Note that action VIEWED is worth 5 points
        //Note that action ADDED_TO_CART is worth 10 points
        //Note that action PURCHASED is worth 20 points
        $action_one = "VIEWED";
        $action_two = "ADDED_TO_CART";
        $action_three = "PURCHASED";

        //Grab the ids of the first three items
        $item_one_id = ItemProfile::find(1)->id;
        $item_two_id = ItemProfile::find(2)->id;
        $item_three_id = ItemProfile::find(3)->id;

        //Perform three user actions, each to a different item
        event(new UserAction($user_id, $item_one_id, $action_one));
        event(new UserAction($user_id, $item_two_id, $action_two));
        event(new UserAction($user_id, $item_three_id, $action_three));

        $itemProfiles = UserProfile::with('itemProfiles')->first()->itemProfiles()->orderByPivot('preference', 'DESC')->take(10)->get();
        foreach($itemProfiles as $itemProfile){
            $preference = $itemProfile->pivot->preference;
            echo("\nItem preference for Item Profile: \n$itemProfile->id is: $preference");
        }
    }
}