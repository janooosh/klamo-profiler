<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ProfilingTagType;
use Klamo\ProfilingSystem\Models\TestAttribute;
use Klamo\ProfilingSystem\Models\TestProduct;
use Klamo\ProfilingSystem\Tests\TestCase;


class SetupProfilingTagsTest extends TestCase{

    use RefreshDatabase;

    // Test the mass generation of profiling tags via the setup sub system
    public function test_profiling_tag_generation_via_setup_sub_system()
    {
        $this->withoutExceptionHandling();

        //Use test attribute as the "product attribute" for testing purposes
        //Create 1000 models
        TestAttribute::factory(1000)->create();

        //The column name which will be used for the profiling tag name
        $column_name = 'name';
        //The profiling tag type name which the created profiling tags will fall under
        $profiling_tag_type_name = 'new_tags';

        $this->assertCount(1000, TestAttribute::all());
        KlamoProfiler::setup()->profilingTag()->generate(TestAttribute::class, $column_name, $profiling_tag_type_name);

        $this->assertCount(1000, ProfilingTag::all());
    }

    // Test that the create method of the setup of profiling tags works
    public function test_setup_profiling_tag_create_method()
    {
        //Assert that no profiling tags exist
        $this->assertCount(0, ProfilingTag::all());

        //Profiling tag values
        $name = 'tag';
        $type = 'tag_type';
        KlamoProfiler::setup()->profilingTag()->create(name: $name, profiling_tag_type: $type);

        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTag::all());

        //Grab the profiling tag and assert that values are correct
        $profiling_tag = ProfilingTag::first();

        $this->assertEquals($name, $profiling_tag->name);
        $this->assertEquals($type, $profiling_tag->profilingTagType->name);
    }

    // Test that the read method of the setup of profiling tags works
    public function test_setup_profiling_tag_read_method()
    {
        //Assert that no profiling tags exist
        $this->assertCount(0, ProfilingTag::all());

        //Create a profiling tag with the following parameters:
        $name = 'tag';
        $profiling_tag_type_name = 'tag_type';
        $profiling_tag_type = ProfilingTagType::create([
            'name' => $profiling_tag_type_name,
        ]);
        $this->assertCount(1, ProfilingTagType::all());
        //Grab the profiling tag and assert that values are correct
        ProfilingTag::factory()->create([
            'name' => $name,
            'profiling_tag_type_id' => $profiling_tag_type->id,
        ]);

        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTag::all());

        //Grab the profiling tag using Klamo profiler read method
        $profiling_tag = KlamoProfiler::setup()->profilingTag()->read(profiling_tag_name: $name,profiling_tag_type_name: $profiling_tag_type_name);

        $this->assertEquals($name, $profiling_tag->name);
        $this->assertEquals($profiling_tag_type->id, $profiling_tag->profiling_tag_type_id);
    }

    // Test that the delete method of the setup of profiling tags works
    public function test_setup_profiling_tag_delete_method()
    {
        //Assert that no profiling tags exist
        $this->assertCount(0, ProfilingTag::all());

        //Create a profiling tag with the following parameters:
        $name = 'tag';
        $profiling_tag_type_id = 1;
        //Grab the profiling tag and assert that values are correct
        $profiling_tag = ProfilingTag::factory()->create([
            'name' => $name,
            'profiling_tag_type_id' => $profiling_tag_type_id,
        ]);

        //Assert that a profiling tag was created
        $this->assertCount(1, ProfilingTag::all());

        //Grab the profiling tag using the delete method
        KlamoProfiler::setup()->profilingTag()->delete($profiling_tag->id);

        //Assert that no profiling tags exist anymore
        $this->assertCount(0, ProfilingTag::all());

        $this->assertDatabaseMissing('profiling_tags', [
            'id' => $profiling_tag->id,
        ]);
    }

    public function test_small()
    {
        TestProduct::factory(100)->create();

        KlamoProfiler::setup()->productProfile()->generate(TestProduct::class);

        $this->assertCount(100, ProductProfile::all());

        $profile = ProductProfile::first();

        $tag_id = ProfilingTag::factory()->create()->id;
        KlamoProfiler::setup()->productProfile()->addProfilingTag(1,$tag_id);
        $tags = 'profilingTags';
        $profiling_tags = $profile->$tags;
        $profiling_tags = $profiling_tags->pluck('name');
        echo("\n$profiling_tags");
    }
}