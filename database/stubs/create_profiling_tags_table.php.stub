<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Klamo\ProfilingSystem\Models\ProfilingTagType;

class CreateProfilingTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('profiling_tags')){
            Schema::create('profiling_tags', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignIdFor(ProfilingTagType::class, 'profiling_tag_type_id');
                $table->integer('viewed')->default(0);
                $table->integer('added_to_cart')->default(0);
                $table->integer('purchased')->default(0);
                $table->unique(['name', 'profiling_tag_type_id']);
                $table->integer('weight_factor')->default(1);
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
        Schema::dropIfExists('profiling_tags');
    }
}
