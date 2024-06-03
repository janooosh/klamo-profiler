<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProfilingTag;
use Klamo\ProfilingSystem\Models\ConsumerProfile;

class CreateConsumerProfileProfilingTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('consumer_profile_profiling_tag')){
            Schema::create('consumer_profile_profiling_tag', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(ProfilingTag::class,'consumer_profiling_tag_id')->index('consumer_profiling_tag_id');
                $table->foreignIdFor(ConsumerProfile::class)->index('consumer_profile_id');
                $table->integer('actions')->default(0);
                $table->integer('points')->default(0);
                $table->integer('weight')->default(0);
                $table->unique(['consumer_profiling_tag_id','consumer_profile_id'], 'consumer_tag_unique_id');
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
        Schema::dropIfExists('consumer_profile_profiling_tag');
    }
}
