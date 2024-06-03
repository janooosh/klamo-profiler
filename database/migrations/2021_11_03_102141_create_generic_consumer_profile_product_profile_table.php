<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProductProfile;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;

class CreateGenericConsumerProfileProductProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('generic_consumer_profile_product_profile')){
            Schema::create('generic_consumer_profile_product_profile', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(GenericConsumerProfile::class);
                $table->foreignIdFor(ProductProfile::class,'generic_consumer_product_profile_id')->index('generic_consumer_product_profile_id');
                $table->integer('preference')->default(0);
                $table->unique(['generic_consumer_product_profile_id','generic_consumer_profile_id'], 'generic_product_unique_id');
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
        Schema::dropIfExists('generic_consumer_profile_product_profile');
    }
}
