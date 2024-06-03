<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ConsumerAction;
use Klamo\ProfilingSystem\Models\ConsumerProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ProfilingTagType;
use Klamo\ProfilingSystem\Models\TestAttribute;
use Klamo\ProfilingSystem\Models\TestConsumer;
use Klamo\ProfilingSystem\Models\TestProduct;
use Klamo\ProfilingSystem\Tests\TestCase;

class ConsumerActionFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Assert that a consumer action properly updates the points of the target products profiling tags
     */
    public function test_consumer_action_properly_updates_the_points_of_the_target_products_profiling_tags()
    {
        $this->withoutExceptionHandling();

        //Create A test consumer
        $test_consumer = TestConsumer::factory()->create();

        //Create 9 test attributes
        TestAttribute::factory(9)->create();

        //Create 3 test products
        TestProduct::factory(3)->create();

        // Assert that none of the following exist: 
        // No profiling tag types
        $this->assertCount(0, ProfilingTagType::all());
        // No profiling tag
        $this->assertCount(0, ProfilingTag::all());
        // No product profiles
        $this->assertCount(0, ProductProfile::all());
        // No consumer profiles
        $this->assertCount(0, ConsumerProfile::all());
        // No generic consumer profiles
        $this->assertCount(0, GenericConsumerProfile::all());

        // Initialize system using klamo profiler
        // A single profiling tag type
        $profiling_tag_type = 'color';
        KlamoProfiler::setup()->profilingTagType()->create(profiling_tag_type_name: $profiling_tag_type);
        $this->assertCount(1, ProfilingTagType::all());
        // All profiling tags belonging to the previously created tag type
        KlamoProfiler::setup()->profilingTag()->generate(attribute_class: TestAttribute::class, column_name:'name', profiling_tag_type: $profiling_tag_type);
        $this->assertCount(9, ProfilingTag::all());
        // All product profiles
        KlamoProfiler::setup()->productProfile()->generate(TestProduct::class);
        $this->assertCount(3, ProductProfile::all());
        // All consumer profiles
        KlamoProfiler::setup()->consumerProfile()->generate(TestConsumer::class);
        $this->assertCount(1, ConsumerProfile::all());
        // All consumer profiles
        KlamoProfiler::setup()->genericConsumerProfile()->generate();
        $this->assertCount(1, ConsumerProfile::all());
       
        //Attach the first 3 profiling tags to the first item
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 1, profiling_tag_id: 1);
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 1, profiling_tag_id: 2);
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 1, profiling_tag_id: 3);
        $product_profile_first = ProductProfile::find(1);
        //Assert relationships between profiles and profiling tags for set one
        $this->assertTrue($product_profile_first->profilingTags->contains(1));
        $this->assertTrue($product_profile_first->profilingTags->contains(2));
        $this->assertTrue($product_profile_first->profilingTags->contains(3));
        
        //Attach the second 3 profiling tags to the second item
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 2, profiling_tag_id: 4);
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 2, profiling_tag_id: 5);
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 2, profiling_tag_id: 6);
        $product_profile_second = ProductProfile::find(2);
        //Assert relationships between profiles and profiling tags for set one
        $this->assertTrue($product_profile_second->profilingTags->contains(4));
        $this->assertTrue($product_profile_second->profilingTags->contains(5));
        $this->assertTrue($product_profile_second->profilingTags->contains(6));

        //Attach the third 3 profiling tags to the third item
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 3, profiling_tag_id: 7);
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 3, profiling_tag_id: 8);
        KlamoProfiler::setup()->productProfile()->addProfilingTag(product_id: 3, profiling_tag_id: 9);
        $product_profile_third = ProductProfile::find(3);
        //Assert relationships between profiles and profiling tags for set one
        $this->assertTrue($product_profile_third->profilingTags->contains(7));
        $this->assertTrue($product_profile_third->profilingTags->contains(8));
        $this->assertTrue($product_profile_third->profilingTags->contains(9));

        
        //Count of all profiling tags
        $tag_count = ProfilingTag::count();
        
        //Grab the first consumer profile
        $consumer_profile = ConsumerProfile::with('profilingTags')->first();
        
        //Assert that the profile has all possible profiling tags
        $this->assertEquals($tag_count, $consumer_profile->profilingTags()->count());
        
        //Grab the first generic consumer profile
        $generic_consumer_profile = GenericConsumerProfile::with('profilingTags')->first();
        
        //Assert that the profile has all possible profiling tags
        $this->assertEquals($tag_count, $generic_consumer_profile->profilingTags()->count());

        //A series of user actions will occur by this user for the following actions: 
        $action_one = "VIEWED";
        $action_two = "ADDED_TO_CART";
        $action_three = "PURCHASED";

        //With the following weights:
        $weight_one = 5;
        $weight_two = 10;
        $weight_three = 20;

        ConsumerAction::create([
            'name' => $action_one,
            'weight' => $weight_one,
        ]);

        ConsumerAction::create([
            'name' => $action_two,
            'weight' => $weight_two,
        ]);

        ConsumerAction::create([
            'name' => $action_three,
            'weight' => $weight_three,
        ]);

        //Assert that consumer actions were created: 
        $this->assertCount(3, ConsumerAction::all());
        
        //Perform consumer actions:
        KlamoProfiler::gather()->consumerAction(profile_type: ConsumerProfile::class, consumer_id: 1, product_id: $product_profile_first->id, consumer_action: $action_one);
        KlamoProfiler::gather()->consumerAction(profile_type: ConsumerProfile::class, consumer_id: 1, product_id: $product_profile_second->id, consumer_action: $action_two);
        KlamoProfiler::gather()->consumerAction(profile_type: ConsumerProfile::class, consumer_id: 1, product_id: $product_profile_third->id, consumer_action: $action_three);
        
        $profiling_tags = $consumer_profile->profilingTags()->orderBy('id')->get();
        foreach($profiling_tags as $profiling_tag){
            $current_points = $consumer_profile->profilingTags()->find($profiling_tag)->pivot->points;
            $current_weight = $consumer_profile->profilingTags()->find($profiling_tag)->pivot->weight;
            echo "\nCurrent points for tag with id: $profiling_tag->id is: $current_points";
            echo "\nCurrent weight for tag with id: $profiling_tag->id is: $current_weight";
            echo "\n______________________________________";
        }
        $preference_one = $consumer_profile->productProfiles()->find(1)->pivot->preference;
        $preference_two = $consumer_profile->productProfiles()->find(2)->pivot->preference;
        $preference_three = $consumer_profile->productProfiles()->find(3)->pivot->preference;
        echo("\nPreference of the first product is: $preference_one");
        echo("\nPreference of the second product is: $preference_two");
        echo("\nPreference of the third product is: $preference_three");

        //Assert values in users profiles are updated
        $this->assertEquals(5, $consumer_profile->profilingTags()->find(1)->pivot->points);
        $this->assertEquals(5, $consumer_profile->profilingTags()->find(2)->pivot->points);
        $this->assertEquals(5, $consumer_profile->profilingTags()->find(3)->pivot->points);
        $this->assertEquals(10, $consumer_profile->profilingTags()->find(4)->pivot->points);
        $this->assertEquals(10, $consumer_profile->profilingTags()->find(5)->pivot->points);
        $this->assertEquals(10, $consumer_profile->profilingTags()->find(6)->pivot->points);
        $this->assertEquals(20, $consumer_profile->profilingTags()->find(7)->pivot->points);
        $this->assertEquals(20, $consumer_profile->profilingTags()->find(8)->pivot->points);
        $this->assertEquals(20, $consumer_profile->profilingTags()->find(9)->pivot->points);
    }
}
