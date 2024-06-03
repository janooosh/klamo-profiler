<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Klamo\ProfilingSystem\Database\Factories\TestAttributeFactory;
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

class SystemSetupScenarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_setup_load()
    {
        $this->withoutExceptionHandling();

        echo("\nBeginning System load test");
        echo("\n---------------------------");

        echo("\nAsserting empty database");
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
        echo("\nPASSED!");
        echo("\n---------------------------");

        echo("\nPerforming system setup.");
        echo("\n---------------------------");
        //Get time before system setup
        $start_time = now();

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
            
        //Get end time
        $end_time = now();

        echo("\nSystem setup completed!");
        echo("\n---------------------------");

        echo("\nEvaluating proper setup.");
        echo("\n---------------------------");
        $this->assertCount(1000, ProfilingTag::all());
        $this->assertCount(10000, ProductProfile::all());
        $this->assertCount(2000, ConsumerProfile::all());


        echo("\nSystem setup was successful!");
        echo("\n---------------------------");

        echo("\nEvaluating time for system setup is under 30 minutes!");
        echo("\n---------------------------");

        //Calculate time diff for a new product
        $time_elapsed_in_minutes = (float)($end_time->diffInMinutes($start_time));
        $time_elapsed_in_seconds = (float)($end_time->diffInSeconds($start_time));
        $this->assertTrue($time_elapsed_in_minutes < 60);

        echo("\nSuccess!");
        echo("\n---------------------------");

        echo("\nCreating a single new product.");
        echo("\n---------------------------");
        $new_product = TestProduct::factory()->create();

        //Get time before product profile generation
        $start_time = now();

        KlamoProfiler::setup()->productProfile($new_product->id);

        //Get time after product profile was generated
        $end_time = now();
        
        echo("\nMeasure time for single product creation.");
        //Calculate time diff for calculations
        $time_elapsed_new_product = (float)($end_time->diffInMicroseconds($start_time));
        echo("\n---------------------------");
        echo("\n---------------------------");
        
        echo("\nTotal time for system setup in minutes: $time_elapsed_in_minutes");
        echo("\nTotal time for system setup in seconds: $time_elapsed_in_seconds");
        echo("\nTime for new product profile generation after system setup in microseconds: $time_elapsed_new_product");
        echo("\n---------------------------");
    }
}
