<?php

namespace Klamo\ProfilingSystem\Repositories\ProductProfile;

use Klamo\ProfilingSystem\Facades\KlamoProfiler;
use Klamo\ProfilingSystem\Jobs\UpdateProfilingTagStatus;
use Klamo\ProfilingSystem\Jobs\GenerateProductProfiles;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Illuminate\Support\Facades\Log;

class ProductProfileRepository implements IProductProfileRepository{

    /**
     * Generate
     * 
     * Mass Generates product profiles based on:
     *  - A collection of products
     * 
     * The mass generation is passed to a batch of jobs.
     * Returns the batch object.
     * 
     * @param products
     * 
     * @return Klamo\ProfilingSystem\Jobs\GenerateProductProfiles
     */
    public function generate($products)
    {
        // Dispatch a job which will create product profiles for all entries of the given collection
        return new GenerateProductProfiles($products);
    }

    /**
     * Create
     * 
     * Creates a new Product Profile based on:
     *  - The id of the product
     * 
     * Create if it doesn't exist or update the existing one.
     * Return the product profile id
     * 
     * @param product_id
     * 
     * @return \Klamo\ProfilingSystem\Models\ProductProfile or null
     */
    public function create($product_id)
    {
        if(!$product_id || !intval($product_id) || (int)$product_id < 1) {
            Log::error("[ProductProfile] Passed invalid value to Create. Passed Value: ".$product_id);
            return null;
        }
        $product_profile = ProductProfile::updateOrCreate([
            'product_id' => $product_id
        ]);
        $profiling_tags = ProfilingTag::pluck('id');
        $product_profile->profilingTags()->sync($profiling_tags);
                    
        //TODO Log creation/errors
        return $product_profile;
    }

    /**
     * Read 
     * 
     * Given a product id, return its product profile
     * 
     * @param product_profile_id
     * 
     * @return \Klamo\ProfilingSystem\Models\ProductProfile
     */
    public function read(?Int $product_id)
    {
        //If product id is not null, then find its profile and return it
        if($product_id) {
            return ProductProfile::where('product_id', $product_id)->first();
        }
        return null;
    }

    /**
     * UpdateWithProfilingTags
     * 
     * Given a collection of products, return a job
     * The job updates the product profile with the enabled status of profiling tags
     * 
     * @param products
     * 
     * @return Klamo\ProfilingSystem\Jobs\UpdateProfilingTagStatus
     */
    public function updateWithProfilingTags($products)
    {
        return new UpdateProfilingTagStatus($products);
    }

    /**
     * IncrementViewedCount
     * 
     * Given a product id, increment the viewed count
     * 
     * @param product_id
     * 
     * @return void
     */
    public function incrementViewedCount(?Int $product_id)  
    {
        //Get product profile
        $product_profile = $this->read($product_id);

        if($product_profile){
            $product_profile->increment('viewed');
        }
    }

    /**
     * IncrementAddedToCartCount
     * 
     * Given a product id, increment the viewed count
     * 
     * @param product_id
     * 
     * @return void
     */
    public function incrementAddedToCartCount(?Int $product_id)  
    {
        //Get product profile
        $product_profile = $this->read($product_id);

        if($product_profile){
            $product_profile->increment('added_to_cart');
        }
    }

    /**
     * IncrementPurchasedCount
     * 
     * Given a product id, increment the viewed count
     * 
     * @param product_id
     * 
     * @return void
     */
    public function incrementPurchasedCount(?Int $product_id)  
    {
        //Get product profile
        $product_profile = $this->read($product_id);

        if($product_profile){
            $product_profile->increment('purchased');
        }
    }

    /**
     * Delete
     * 
     * Given a product id, delete its product profile
     * 
     * @param product_id
     * 
     * @return boolean
     */
    public function delete(?Int $product_id)
    {
        //Grab consumer profile based on consumer id and return false if it doesn't exist
        $product_profile = $this->read($product_id);
        if($product_profile){
            //Attempt to delete the consumer profile and return the result
            return $product_profile->delete();
        }
        return false;
    }
}