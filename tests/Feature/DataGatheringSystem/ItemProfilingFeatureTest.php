<?php

namespace Klamo\ProfilingSystem\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Klamo\ProfilingSystem\Events\ItemProfileTagUpdate;
use Klamo\ProfilingSystem\Events\ItemWasCreated;
use Klamo\ProfilingSystem\Events\ItemWasDeleted;
use Klamo\ProfilingSystem\Listeners\DeleteItemProfile;
use Klamo\ProfilingSystem\Listeners\UpdateItemProfile;
use Klamo\ProfilingSystem\Models\ItemProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Tests\TestCase;

class ItemProfilingFeatureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Assert that an item profile is created, when an item is created
     */
    public function test_item_profile_created_on_item_created_event_test()
    {
        $this->withoutExceptionHandling();

        //No profiling tags exist to begin with
        $this->assertCount(0, ItemProfile::all());

        //An Item was created with following id
        $item_id = 1;
        
        event(new ItemWasCreated($item_id));

        //Assert that an item profile has been created
        $this->assertCount(1, ItemProfile::all());
         
        //Grab the first item profile
        $item_profile = ItemProfile::first();
        $this->assertEquals(1, $item_profile->item_id);
    }

    /**
     * Assert that an item profile is updated with an added tag
     */
    public function test_item_profile_updated_with_added_tag()
    {
        $this->withoutExceptionHandling();

        //The product that is updated has an id of
        $item_id = 1;

        $item_profile = ItemProfile::create([
            'item_id' => $item_id,
        ]);

        $profiling_tag = ProfilingTag::create([
            'name' => 'blue',
            'type_id' => 1,
        ]);

        //The product was updated with an attribute with the following name
        $tag_name = "blue";
        //Of the following type
        $tag_type = 'color';
        //Action type
        $action_type = 'ADD';

        (new UpdateItemProfile())->handle(new ItemProfileTagUpdate($item_id, $tag_name, $tag_type, $action_type));
        
        $item_profile->refresh();

        //Assert relationship between
        $this->assertEquals(1, $item_profile->profilingTags()->count());
        $this->assertTrue($item_profile->profilingTags->contains($profiling_tag));
    }

    /**
     * Assert that an item profile is updated with a removed tag
     */
    public function test_item_profile_updated_with_removed_tag()
    {
        $this->withoutExceptionHandling();

        //The product that is updated has an id of
        $item_id = 1;

        $item_profile = ItemProfile::create([
            'item_id' => $item_id,
        ]);

        $profiling_tag = ProfilingTag::create([
            'name' => 'blue',
            'type_id' => 1,
        ]);

        //The product was updated with an attribute with the following name
        $tag_name = "blue";
        //Of the following type
        $tag_type = 'color';
        //Action type
        $action_type = 'REMOVE';

        $item_profile->profilingTags()->attach($profiling_tag->id);
        //Assert relationship between
        $this->assertEquals(1, $item_profile->profilingTags()->count());
        $this->assertTrue($item_profile->profilingTags->contains($profiling_tag));

        (new UpdateItemProfile())->handle(new ItemProfileTagUpdate($item_id, $tag_name, $tag_type, $action_type));
        
        $item_profile->refresh();

        //Assert no relationship between
        $this->assertEquals(0, $item_profile->profilingTags()->count());
        $this->assertFalse($item_profile->profilingTags->contains($profiling_tag));
    }

   

    /**
     * Assert that an item profile is deleted, when an item is deleted
     */
    public function test_item_profile_deleted_on_item_deleted_event_test()
    {
        $this->withoutExceptionHandling();
        Event::fake();

        //The product that is deleted has an id of
        $item_id = 1;

        $item_profile = ItemProfile::create([
            'item_id' => $item_id,
        ]);
        
        (new DeleteItemProfile())->handle(new ItemWasDeleted($item_id));
        //No item profiles exist to
        $this->assertCount(0, ItemProfile::all());
    }
}
