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


class ItemRecommendationTest extends TestCase{

    use RefreshDatabase;

    /**
     * Assert that a list of item recommendations is properly generated after a users actions
     */
    public function test_that_list_of_item_recommendeations_generated_after_user_action(){
        
        $this->withoutExceptionHandling();

        //Begin preparations
        //First Assert that no profiling tags, item profiles, user profiles and normalized user profiles exist
        $this->assertCount(0, ProfilingTag::all());
        $this->assertCount(0, ItemProfile::all());
        $this->assertCount(0, UserProfile::all());
        $this->assertCount(0, NormalizedUserProfile::all());

        //Create 4 Items with 1 profiling tag each
        ItemProfile::factory(4)->hasProfilingTags(1)->create();
        
        //Assert that 4 item profiles were created
        $this->assertCount(4, ItemProfile::all());

        //Assert that 4 profiling tags were created
        $this->assertCount(4, ProfilingTag::all());

        //Create a user profile with a normalized profile from a user id
        $user_id = 1;
        event(new UserWasCreated($user_id));
        event(new NewGenericUserProfile('random_month', 'random_year'));
        
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

        //Grab the user profile and normalized user profile
        $user_profile = UserProfile::first();
        $normalized_user_profile = NormalizedUserProfile::first();

        //Assert the proper points were attached to the values of the tags for the user profile
        $this->assertEquals(5, $user_profile->profilingTags()->find(1)->pivot->value);
        $this->assertEquals(10, $user_profile->profilingTags()->find(2)->pivot->value);
        $this->assertEquals(20, $user_profile->profilingTags()->find(3)->pivot->value);
        $this->assertEquals(0, $user_profile->profilingTags()->find(4)->pivot->value);

        //Assert the proper weight were attached to the values of the tags for the normalized user profile
        //Note that the total points are 35, therefore
        //A weight of 5/35 = 0.14, in integer value = 14
        //A weight of 10/35 = 0.28, in integer value = 28
        //A A weight of 20/35 = 0.57, in integer value = 57
        $this->assertEquals(14, $normalized_user_profile->profilingTags()->find(1)->pivot->value);
        $this->assertEquals(28, $normalized_user_profile->profilingTags()->find(2)->pivot->value);
        $this->assertEquals(57, $normalized_user_profile->profilingTags()->find(3)->pivot->value);
        $this->assertEquals(0, $normalized_user_profile->profilingTags()->find(4)->pivot->value);

        //During this test, three different user actions were performed, each to a different item
        //As there are only four items, we expect the value to be:
        // 3,2,1,4
        //Grab the recommendations in order for this user profile
        $recommendations = $user_profile->itemProfiles()->orderByPivot('preference', 'DESC')->get();

        //Assert recommendations are not empty
        $this->assertNotEmpty($recommendations);
        //Assert count of recommendations
        $this->assertCount(4, $recommendations);
        
        //Assert preferences
        $this->assertEquals(57, $recommendations[0]->pivot->preference);
        $this->assertEquals(28, $recommendations[1]->pivot->preference);
        $this->assertEquals(14, $recommendations[2]->pivot->preference);
        $this->assertEquals(0, $recommendations[3]->pivot->preference);

        //Assert correct ids in order
        $recommendation_ids = $user_profile->itemProfiles()->orderByPivot('preference', 'DESC')->pluck('item_profile_id');
        //Assert recommendation ids collection is not empty
        $this->assertNotEmpty($recommendation_ids);
        echo("\n");
        echo("\nRecommendation ids: $recommendation_ids");
        $this->assertEquals(3, $recommendation_ids[0]);
        $this->assertEquals(2, $recommendation_ids[1]);
        $this->assertEquals(1, $recommendation_ids[2]);
        $this->assertEquals(4, $recommendation_ids[3]);
    }
}