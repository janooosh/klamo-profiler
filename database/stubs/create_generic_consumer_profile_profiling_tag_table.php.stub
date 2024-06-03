<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\GenericConsumerProfile;
use Klamo\ProfilingSystem\Models\ProfilingTag;

class CreateGenericConsumerProfileProfilingTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('generic_consumer_profile_profiling_tag')){
            Schema::create('generic_consumer_profile_profiling_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(ProfilingTag::class,'generic_consumer_profiling_tag_id')->index('generic_consumer_profiling_tag_id');
                $table->foreignIdFor(GenericConsumerProfile::class)->index('generic_consumer_profile_id');
                $table->integer('actions')->default(0);
                $table->integer('points')->default(0);
                $table->integer('weight')->default(0);
                $table->unique(['generic_consumer_profiling_tag_id', 'generic_consumer_profile_id'], 'generic_tag_unique_id');
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
        Schema::dropIfExists('generic_consumer_profile_profiling_tag');
    }
}
