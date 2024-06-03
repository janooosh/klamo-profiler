<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class CreateProductProfileProfilingTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('product_profile_profiling_tag')){
            Schema::create('product_profile_profiling_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(ProductProfile::class)->index('product_profile_id');
                $table->foreignIdFor(ProfilingTag::class, 'product_profiling_tag_id')->index('product_profiling_tag_id');
                $table->integer('enabled')->default(0);
                $table->unique(['product_profile_id','product_profiling_tag_id'],'product_tag_unique_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_profile_profiling_tag');
    }
}
