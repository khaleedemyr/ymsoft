<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemAvailabilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('item_availabilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->enum('availability_type', ['all', 'region', 'outlet']);
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedInteger('outlet_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('outlet_id')->references('id_outlet')->on('tbl_data_outlet')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_availabilities');
    }
} 