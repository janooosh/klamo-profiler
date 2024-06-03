<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;


class ProfilingTagModelUnitTest extends TestCase{

    use RefreshDatabase;
    /**
     * Assert that the profiling tag table exists
     */
    public function test_profiling_tag_table_exists_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasTable('profiling_tags');
        $name_column_exists  = Schema::hasColumn('profiling_tags', 'name');
        $viewed_column_exists  = Schema::hasColumn('profiling_tags', 'viewed');
        $cart_column_exists  = Schema::hasColumn('profiling_tags', 'added_to_cart');
        $purchased_column_exists  = Schema::hasColumn('profiling_tags', 'purchased');
        $weight_factor_column_exists  = Schema::hasColumn('profiling_tags', 'weight_factor');
        $type_id_column_exists  = Schema::hasColumn('profiling_tags', 'profiling_tag_type_id');
        $this->assertTrue($table_exists);
        $this->assertTrue($name_column_exists);
        $this->assertTrue($viewed_column_exists);
        $this->assertTrue($cart_column_exists);
        $this->assertTrue($purchased_column_exists);
        $this->assertTrue($weight_factor_column_exists);
        $this->assertTrue($type_id_column_exists);

    }

    /**
     * Assert that the profiling tag table has column name
     */
    public function test_profiling_tag_table_has_column_name_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tags', 'name');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the profiling tag table has column profiling tag type id
     */
    public function test_profiling_tag_table_has_column_profiling_tag_type_id_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tags', 'profiling_tag_type_id');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the profiling tag table has column viewed
     */
    public function test_profiling_tag_table_has_column_viewed_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tags', 'viewed');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the profiling tag table has column added to cart
     */
    public function test_profiling_tag_table_has_column_added_to_cart_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tags', 'added_to_cart');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the profiling tag table has column purchased
     */
    public function test_profiling_tag_table_has_column_purchased_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tags', 'purchased');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that a profiling tag type can be created
     */
    public function test_profiling_tag_type_can_be_created()
    {
        $this->withoutExceptionHandling();

        // No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());

        // Parameters to be inserted
        $name = 'pants';
        $profiling_tag_type_id = 5;
        $viewed = 100;
        $added_to_cart = 10;
        $purchased = 1;

        ProfilingTag::create([
            'name' => $name,
            'profiling_tag_type_id' => $profiling_tag_type_id,
            'viewed' => $viewed,
            'added_to_cart' => $added_to_cart,
            'purchased' => $purchased,
        ]);

        //1 Profiling tag should now exist
        $this->assertCount(1, ProfilingTag::all());

        // Grab first profiling tag
        $profiling_tag = ProfilingTag::first();

        // Assert that name and weight is correct
        $this->assertEquals($name, $profiling_tag->name);
        $this->assertEquals($profiling_tag_type_id, $profiling_tag->profiling_tag_type_id);
        $this->assertEquals($viewed, $profiling_tag->viewed);
        $this->assertEquals($added_to_cart, $profiling_tag->added_to_cart);
        $this->assertEquals($purchased, $profiling_tag->purchased);
    }

    /**
     * Assert profiling tag type can be updated
     */
    public function test_profiling_tag_type_can_be_updated()
    {
        $this->withoutExceptionHandling();

        // No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());

        // Parameters to be inserted
        $name = 'pants';
        $profiling_tag_type_id = 5;
        $viewed = 100;
        $added_to_cart = 10;
        $purchased = 1;

        ProfilingTag::create([
            'name' => $name,
            'profiling_tag_type_id' => $profiling_tag_type_id,
            'viewed' => $viewed,
            'added_to_cart' => $added_to_cart,
            'purchased' => $purchased,
        ]);

        //1 Profiling tag should now exist
        $this->assertCount(1, ProfilingTag::all());

        // Grab first profiling tag
        $profiling_tag = ProfilingTag::first();

        // New parameters to be inserted
        // Parameters to be inserted
        $new_name = 'blue';
        $new_profiling_tag_type_id = 3;
        $new_viewed = 200;
        $new_added_to_cart = 20;
        $new_purchased = 2;

        $profiling_tag->update([
            'name' => $new_name,
            'profiling_tag_type_id' => $new_profiling_tag_type_id,
            'viewed' => $new_viewed,
            'added_to_cart' => $new_added_to_cart,
            'purchased' => $new_purchased,
        ]);

        $profiling_tag->refresh(); 
        $this->assertEquals($new_name, $profiling_tag->name);
        $this->assertEquals($new_profiling_tag_type_id, $profiling_tag->profiling_tag_type_id);
        $this->assertEquals($new_viewed, $profiling_tag->viewed);
        $this->assertEquals($new_added_to_cart, $profiling_tag->added_to_cart);
        $this->assertEquals($new_purchased, $profiling_tag->purchased);
    }

    /**
     * Assert that profiling tag can be deleted
     */
    public function test_profiling_tag_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTag::all());

        //Create a profiling tag using its factory
        $profiling_tag = ProfilingTag::factory()->create();

        $profiling_tag_id = $profiling_tag->id;

        $profiling_tag->delete();

        $this->assertDatabaseMissing('profiling_tags', [
            'id' => $profiling_tag_id
        ]);
    }
}