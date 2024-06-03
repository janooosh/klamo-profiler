<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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

class RecommendationsScenarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Assert that a consumer action properly updates the points of the target products profiling tags
     */
    public function test_consumer_action_properly_updates_the_points_of_the_target_products_profiling_tags()
    {
        $this->withoutExceptionHandling();

        echo("\nBeginning product preference calculation load test");
        echo("\n---------------------------");

        //Assert no tag types exist
        $this->assertCount(0, ProfilingTagType::all());
        //Assert no tags exist
        $this->assertCount(0, ProfilingTag::all());
        //Assert no product profiles exist
        $this->assertCount(0, ProductProfile::all());
        //Assert no consumer profiles exist
        $this->assertCount(0, ConsumerProfile::all());
        //Assert no generic profile exist
        $this->assertCount(0, GenericConsumerProfile::all());

        //Creating 1000 test tags
        TestAttribute::factory(1000)->create();
        TestAttribute::all();

        //Creating 10000 test products
        TestProduct::factory(10000)->create();
        $products = TestProduct::all();

        //Creating 2000 test consumers
        TestConsumer::factory(2000)->create();
        $consumers = TestConsumer::all();

        //Create a tag type
        $tag_type = ProfilingTagType::create([
            'name' => 'type',
        ]);

        Bus::batch([
            KlamoProfiler::setup()->profilingTag()->generate(TestAttribute::class, 'name', $tag_type->name),
            KlamoProfiler::setup()->productProfile()->generate($products),
            KlamoProfiler::setup()->consumerProfile()->generate($consumers),
        ])
        ->dispatch();
            
        //Create a consumer action
        KlamoProfiler::setup()->consumerAction()->create('view', 5);
        
        $this->assertCount(1000, ProfilingTag::all());
        $this->assertCount(10000, ProductProfile::all());
        $this->assertCount(2000, ConsumerProfile::all());

        //Grab a product profile
        $product_profile = ProductProfile::first();

        //Grab five random profiling tags and enable them
        $tags = ProfilingTag::inRandomOrder()->limit(5)->get();
        foreach($tags as $tag){
            $product_profile->profilingTags()->updateExistingPivot($tag->id, [
                'enabled' => 1,
            ]);
        }
        
        //Get a consumer profile
        $consumer_profile = ConsumerProfile::first();

        //Get a consumer action
        $consumer_action = ConsumerAction::first();

        echo("\nPerforming a consumer action.");
        echo("\n---------------------------");

        //Perform a consumer action
        KlamoProfiler::gather()->consumerProductAction(
            consumer_id: $consumer_profile->consumer_id,
            product_id: $product_profile->product_id,
            consumer_action:'view'
        );

        //Refresh consumer profile model
        $consumer_profile->refresh();

        echo("\nPerforming product preference calculations.");
        echo("\n---------------------------");
        //Get end time
        $start_time = now();

        Bus::batch([
            KlamoProfiler::process()->calculateConsumerProfilePoints($consumer_profile->consumer_id),
            KlamoProfiler::process()->calculateConsumerProfileWeights($consumer_profile->consumer_id),
            KlamoProfiler::process()->calculateProductPreferences($consumer_profile->consumer_id),
        ])
        ->dispatch();
        
        //Get end time
        $end_time = now();

        //Calculate time diff for calculations
        $time_elapsed_calculating = (float)($end_time->diffInMilliseconds($start_time)/1000);
        
        echo("\nEvaluating that product preference calculation were completed.");
        echo("\n---------------------------");

        //Grab the 5 tags where action was performed
        $tags = $consumer_profile->profilingTags()->orderByPivot('actions', 'DESC')->limit(5)->get();

        foreach($tags as $key => $tag){
            //Assert that actions, points and weight is above 0
            $this->assertTrue($tag->pivot->actions > 0);
            $this->assertTrue($tag->pivot->points > 0);
            $this->assertTrue($tag->pivot->weight > 0);
        }

        
        $product_profile = $consumer_profile->productProfiles()->orderByPivot('preference', 'DESC')->first();
        
        $preference = $product_profile->pivot->preference;
        $this->assertTrue($preference > 0);
        
        echo("\nSuccess!");
        echo("\n---------------------------");
        
        echo("\nTotal time elapsed to perform product preference calculations: $time_elapsed_calculating seconds");
    }
}
