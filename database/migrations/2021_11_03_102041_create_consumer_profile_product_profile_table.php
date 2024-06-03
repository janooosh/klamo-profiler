<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\ConsumerProfile;

class CreateConsumerProfileProductProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('consumer_profile_product_profile')){
            Schema::create('consumer_profile_product_profile', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(ConsumerProfile::class);
                $table->foreignIdFor(ProductProfile::class,'consumer_product_profile_id')->index('consumer_product_profile_id');
                $table->integer('preference')->default(0);
                $table->unique(['consumer_product_profile_id','consumer_profile_id'], 'consumer_product_unique_id');
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
        Schema::dropIfExists('consumer_profile_product_profile');
    }
}
