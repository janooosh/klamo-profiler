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

class ConsumerActionScenarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Assert that a consumer action properly updates the points of the target products profiling tags
     */
    public function test_consumer_action_properly_updates_the_points_of_the_target_products_profiling_tags()
    {
        $this->withoutExceptionHandling();

        echo("\nBeginning consumer action load test");
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

        echo("\nPerforming 10 consumer actions.");
        echo("\n---------------------------");
        //Get end time
        $start_time = now();

        //Perform a consumer action
        for ($x=0; $x<10;$x++) {
            KlamoProfiler::gather()->consumerProductAction(
                consumer_id: $consumer_profile->consumer_id,
                product_id: $product_profile->product_id,
                consumer_action:'view'
            );
        }

        //Get end time
        $end_time = now();
        
        echo("\nCompleted.");
        echo("\n---------------------------");
        
        echo("\nVerify that consumer actions were stored.");
        echo("\n---------------------------");
        //Assert consumer action was documented
        $tags = $consumer_profile->profilingTags()->orderByPivot('actions', 'DESC')->limit(5)->get();
        foreach($tags as $key => $tag){
            $actions = $tag->pivot->actions;
            $this->assertTrue($actions > 0);
        }

        //Calculate time diff for a new tag
        $time_elapsed_new_tag = (float)($end_time->diffInMilliseconds($start_time)/1000);
        echo("\nTotal time elapsed to perform 10 consumer actions: $time_elapsed_new_tag seconds");
    }
}
