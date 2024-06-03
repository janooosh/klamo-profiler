<?php

namespace Klamo\ProfilingSystem\Repositories\ProductProfile;

use Illuminate\Database\Eloquent\Collection;

interface IProductProfileRepository{

    public function generate($products);
    public function create(?Int $product_id);
    public function read(?Int $product_id);
    public function updateWithProfilingTags($products);
    public function incrementViewedCount(?Int $product_id);
    public function incrementAddedToCartCount(?Int $product_id);
    public function incrementPurchasedCount(?Int $product_id);
    public function delete(?Int $product_id);
}