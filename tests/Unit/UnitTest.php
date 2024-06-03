<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Support\Facades\DB;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Jobs\IncrementActionCountOnConsumerProfileProfilingTag;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;

class UnitTest extends TestCase{

    /** @test */
    public function test_unit_demo_test(){
        
        $a = [];
        $b = [];

        for ($x = 0; $x <= 1000; $x++) {
            array_push($a, rand(0,1));
            array_push($b, rand(0,20));
          }
        
        for ($x = 0; $x <= 1000; $x++) {
            $new_array = array_map(function ($x, $y) {
                return $x*$y;
            }, $a, $b);

            $sum = array_sum($new_array);
        }   
        // foreach($new_array as $ah){
        //     echo("\n$ah");
        // }
        echo("\n$sum");

        $this->assertTrue(false);
    }

    /** @test */
    public function test_unit_demo_test_two(){
        
        //Default consumers actions
        $action_one = "VIEWED";
        $action_two = "ADDED_TO_CART";
        $action_three = "PURCHASED";

        //With the default weights
        $weight_one = 5;
        $weight_two = 10;
        $weight_three = 20;

        KlamoProfiler::setup()->ConsumerAction()->create(consumer_action_name: $action_one, consumer_action_weight: $weight_one);
        KlamoProfiler::setup()->ConsumerAction()->create(consumer_action_name: $action_two, consumer_action_weight: $weight_two);
        KlamoProfiler::setup()->ConsumerAction()->create(consumer_action_name: $action_three, consumer_action_weight: $weight_three);
        
        $consumer_profile = ConsumerProfile::factory()->create();
        $generic_consumer_profile = KlamoProfiler::setup()->GenericConsumerProfile()->create();
        $profiling_tags = ProfilingTag::factory(200)->create();
        $product_profiles = ProductProfile::factory(2000)->create();

        $this->assertCount(1, ConsumerProfile::all());
        $this->assertCount(200, ProfilingTag::all());
        $this->assertCount(2000, ProductProfile::all());

        $this->assertCount(200, $consumer_profile->profilingTags);
        $this->assertCount(2000, $consumer_profile->productProfiles);

        $profiling_tag_ids = ProfilingTag::pluck('id');
        
        foreach($product_profiles as $product_profile){
            $product_profile->profilingTags()->sync($profiling_tag_ids);
        }

        $product_profile = $product_profiles->first();
        
        //Increment actions count
        KlamoProfiler::gather()->consumerAction($consumer_profile->consumer_id, $product_profile->product_id, 'VIEWED');
        //IncrementActionCountOnConsumerProfileProfilingTag::dispatch($product_profile, $consumer_profile, 2);
        
        //Calculate points based on action count and tag type
        KlamoProfiler::process()->calculateConsumerProfilePoints($consumer_profile->consumer_id);
        KlamoProfiler::process()->calculateConsumerProfileWeights($consumer_profile->consumer_id);
        KlamoProfiler::process()->calculateProductPreferences($consumer_profile->consumer_id);
        $consumer_profile = ConsumerProfile::first();

        foreach($consumer_profile->productProfiles as $product_profile){
            $pivot = $product_profile->pivot;
            echo("\n$pivot");
        }

        //Calculate product preference
        echo("\nDone creating");
        $this->assertTrue(false);
    }

    public function test_something()
    {
        ConsumerProfile::unsetEventDispatcher();
        ProfilingTag::unsetEventDispatcher();
        $profile = ConsumerProfile::factory()->create();

        $profiling_tag_ids = ProfilingTag::factory(5)->create()->pluck('id');

        $profile->profilingTags()->syncWithoutDetaching($profiling_tag_ids);

        $profiling_tag_count = $profile->profilingTags()->count();
        echo("\n$profiling_tag_count");

        echo("\n------------------------------------------------------------------");

        $profiling_tag_ids = ProfilingTag::factory(5)->create()->pluck('id');

        $profile->profilingTags()->syncWithoutDetaching($profiling_tag_ids);

        $profiling_tag_count = $profile->profilingTags()->count();
        echo("\n$profiling_tag_count");

        echo("\n------------------------------------------------------------------");

        $profile->profilingTags()->syncWithoutDetaching($profiling_tag_ids);
        
        $profiling_tag_count = $profile->profilingTags()->count();
        echo("\n$profiling_tag_count");


        echo("\n------------------------------------------------------------------");

        $column_name = 'viewed';

        $tag = ProfilingTag::first();
        $tag->$column_name++;
        $tag->save();

        $fresh_tag = ProfilingTag::first();
        echo("\n$fresh_tag");
    }
}