<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;


class ProductProfileModelUnitTest extends TestCase{

    use RefreshDatabase;
    /**
     * Assert that the product profiles table exists
     */
    public function test_product_profiles_table_exists_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasTable('product_profiles');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the product profiles table has column product id
     */
    public function test_product_profile_table_has_column_product_id()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('product_profiles', 'product_id');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the product profiles table has column viewed
     */
    public function test_product_profile_table_has_column_viewed()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('product_profiles', 'viewed');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the product profiles table has column added to cart
     */
    public function test_product_profile_table_has_column_added_to_cart()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('product_profiles', 'added_to_cart');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the product profiles table has column purchased
     */
    public function test_product_profile_table_has_column_purchased()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('product_profiles', 'purchased');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that an product profile can be created
     */
    public function test_product_profile_can_be_created()
    {
        $this->withoutExceptionHandling();

        //No product profiles exist to begin with
        $this->assertCount(0, ProductProfile::all());

        ProductProfile::create([
            'product_id' => 1,
        ]);

        //Assert creation
        $this->assertCount(1, ProductProfile::all());
        //Grab first product profile
        $product_profile = ProductProfile::first();
        //Assert correct values
        $this->assertEquals(1, $product_profile->product_id);
    }

    /**
     * Assert that an product profile can be updated with tags
     */
    public function test_product_profile_can_be_updated_with_tags()
    {
        $this->withoutExceptionHandling();

        // Create a new product profile
        $product_profile = ProductProfile::factory()->create();

        // Create a new profiling tag
        $profiling_tag = ProfilingTag::factory()->create();

        // Assert relationship is setup properly
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $product_profile->profilingTags);

        // Set relationship between the two
        $product_profile->profilingTags()->attach($profiling_tag->id);
        
        // Assert that only a single profiling tag is linked and it's the proper one
        $this->assertEquals($profiling_tag->id, $product_profile->profilingTags()->first()->id);
        $this->assertEquals(1, $product_profile->profilingTags()->count());

        // Remove the relationship
        $product_profile->profilingTags()->detach($profiling_tag->id);
        
        // Assert that link between the product profile and the profiling tag is removed
        $this->assertEquals(0, $product_profile->profilingTags()->count());
    }

    /**
     * Assert that a product profile can be deleted
     */
    public function test_product_profile_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling products exist to begin with
        $this->assertCount(0, ProductProfile::all());

        //Create a profiling product
        $product_profile = ProductProfile::factory()->create();

        $product_profile_id = $product_profile->id;

        $product_profile->delete();

        $this->assertDatabaseMissing('product_profiles', [
            'id' => $product_profile_id
        ]);
    }
}