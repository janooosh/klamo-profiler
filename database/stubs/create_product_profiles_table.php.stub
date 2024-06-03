<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('product_profiles')){
            Schema::create('product_profiles', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id')->unique();
                $table->integer('viewed')->default(0);
                $table->integer('added_to_cart')->default(0);
                $table->integer('purchased')->default(0);
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
        Schema::dropIfExists('product_profiles');
    }
}
