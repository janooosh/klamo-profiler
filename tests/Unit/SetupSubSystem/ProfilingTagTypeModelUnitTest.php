<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProfilingTagType;
use Klamo\ProfilingSystem\Tests\TestCase;


class ProfilingTagTypeModelUnitTest extends TestCase{

    use RefreshDatabase;
    /**
     * Assert that the profiling tag types table exists
     */
    public function test_profiling_tag_types_table_exists_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasTable('profiling_tag_types');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the profiling tag types table has column name
     */
    public function test_profiling_tag_types_table_has_column_name_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tag_types', 'name');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that the profiling tag types table has column weight
     */
    public function test_profiling_tag_types_table_has_column_weight_test()
    {
        $this->withoutExceptionHandling();
        
        $table_exists  = Schema::hasColumn('profiling_tag_types', 'weight');
        $this->assertTrue($table_exists);
    }

    /**
     * Assert that a profiling tag type can be created
     */
    public function test_profiling_tag_type_can_be_created()
    {
        $this->withoutExceptionHandling();

        // No profiling tag types exist to begin with
        $this->assertCount(0, ProfilingTagType::all());

        // Parameters to be inserted
        $name = 'category';
        $weight = 5;
        ProfilingTagType::create([
            'name' => $name,
            'weight' => $weight,
        ]);

        //1 Profiling tag type should now exist
        $this->assertCount(1, ProfilingTagType::all());

        // Grab first profiling tag type
        $profiling_tag = ProfilingTagType::first();

        // Assert that name and weight is correct
        $this->assertEquals($name, $profiling_tag->name);
        $this->assertEquals($weight, $profiling_tag->weight);
    }

    /**
     * Assert profiling tag type can be updated
     */
    public function test_profiling_tag_type_can_be_updated()
    {
        $this->withoutExceptionHandling();

        // No profiling tag types exist to begin with
        $this->assertCount(0, ProfilingTagType::all());

        // Parameters to be inserted
        $name = 'category';
        $weight = 5;
        ProfilingTagType::create([
            'name' => $name,
            'weight' => $weight,
        ]);

        //1 Profiling tag type should now exist
        $this->assertCount(1, ProfilingTagType::all());

        // Grab first profiling tag type
        $profiling_tag = ProfilingTagType::first();

        // New parameters to be inserted
        $new_name = 'color';
        $new_weight = 10;

        $profiling_tag->update([
            'name' => $new_name,
            'weight' => $new_weight,
        ]);

        $profiling_tag->refresh(); 
        $this->assertEquals($new_name, $profiling_tag->name);
        $this->assertEquals($new_weight, $profiling_tag->weight);
    }

    /**
     * Assert that profiling tag type can be deleted
     */
    public function test_profiling_tag_type_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, ProfilingTagType::all());

        //Create a profiling tag type using its factory
        $profiling_tag_type = ProfilingTagType::factory()->create();

        $profiling_tag_type_id = $profiling_tag_type->id;

        $profiling_tag_type->delete();

        $this->assertDatabaseMissing('profiling_tag_types', [
            'id' => $profiling_tag_type_id
        ]);
    }
}