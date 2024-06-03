<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ProfilingTagType;
use Klamo\ProfilingSystem\Tests\TestCase;


class SetupProfilingTagTypesTest extends TestCase{

    use RefreshDatabase;

    // Test the mass generation of profiling tag types via the setup sub system
    public function test_profiling_tag_type_generation_via_setup_sub_system()
    {
        $this->withoutExceptionHandling();

        //Create 4 profiling tag type name variables
        $categories = 'categories';
        $colors = 'colors';
        $sizes = 'sizes';
        $values = 'values';

        $profiling_tag_type_names = [];
        array_push($profiling_tag_type_names, $categories);
        array_push($profiling_tag_type_names, $colors);
        array_push($profiling_tag_type_names, $sizes);
        array_push($profiling_tag_type_names, $values);

        //Assert that no profiling tag types exist to begin with
        $this->assertCount(0, ProfilingTagType::all());
        KlamoProfiler::setup()->profilingTagType()->generate(profiling_tag_type_names: $profiling_tag_type_names);

        //Assert that 4 profiling tag types were created
        $this->assertCount(4, ProfilingTagType::all());

        //Assert proper values
        $this->assertEquals($categories, ProfilingTagType::find(1)->name);
        $this->assertEquals($colors, ProfilingTagType::find(2)->name);
        $this->assertEquals($sizes, ProfilingTagType::find(3)->name);
        $this->assertEquals($values, ProfilingTagType::find(4)->name);
    }

    // Test that the create method of the setup of profiling tag types works
    public function test_setup_profiling_tag_type_create_method()
    {
        $this->withoutExceptionHandling();

        //Assert that no profiling tag types exist
        $this->assertCount(0, ProfilingTagType::all());

        //Profiling tag type name variable
        $new_profiling_tag_type = 'special';
        KlamoProfiler::setup()->profilingTagType()->create(profiling_tag_type_name: $new_profiling_tag_type);

        //Assert that a profiling tag type was created
        $this->assertCount(1, ProfilingTagType::all());

        //Grab the profiling tag type and assert that values are correct
        $profiling_tag_type = ProfilingTagType::first();

        $this->assertEquals($new_profiling_tag_type, $profiling_tag_type->name);
    }

    // Test that the read method of the setup of profiling tag types works
    public function test_setup_profiling_tag_type_read_method()
    {
        $this->withoutExceptionHandling();

        //Assert that no profiling tag types exist
        $this->assertCount(0, ProfilingTagType::all());

        //Create a profiling tag type with the following parameters:
        $name = 'trending';

        //Grab the profiling tag type and assert that values are correct
        ProfilingTagType::create([
            'name' => $name,
        ]);

        //Assert that a profiling tag type was created
        $this->assertCount(1, ProfilingTagType::all()); 

        //Grab the profiling tag using the read method
        $fetched_profiling_tag_type = KlamoProfiler::setup()->profilingTagType()->read(profiling_tag_type_name: $name);

        $this->assertEquals($name, $fetched_profiling_tag_type->name);
    }

    //TODO Test update method of the setup of profiling tag types works
    public function test_setup_profiling_tag_type_update_method()
    {
        $this->withoutExceptionHandling();

        $this->assertTrue(true);
    }

    // Test that the delete method of the setup of profiling tag types works
    public function test_setup_profiling_tag_type_delete_method()
    {
        $this->withoutExceptionHandling();

        //Assert that no profiling tags exist
        $this->assertCount(0, ProfilingTagType::all());

        //Create a profiling tag with the following parameters:
        $name = 'special';
        $profiling_tag_type = ProfilingTagType::create([
            'name' => $name,
        ]);

        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTagType::all());

        //Delete the profiling tag using the delete method
        KlamoProfiler::setup()->profilingTagType()->delete($profiling_tag_type->id);

        //Assert that no profiling tags exist anymore
        $this->assertCount(0, ProfilingTagType::all());

        $this->assertDatabaseMissing('profiling_tag_types', [
            'id' => $profiling_tag_type->id,
        ]);
    }
}