<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('sub_category_id')->constrained('sub_categories');
            $table->string('sku', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->foreignId('small_unit_id')->constrained('units');
            $table->foreignId('medium_unit_id')->constrained('units');
            $table->foreignId('large_unit_id')->constrained('units');
            $table->decimal('medium_conversion_qty', 10, 2); // qty konversi ke unit sedang
            $table->decimal('small_conversion_qty', 10, 2);  // qty konversi ke unit kecil
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 50);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('region_id')->constrained('regions');
            $table->decimal('price', 15, 2);
            $table->timestamps();

            $table->unique(['item_id', 'region_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_prices');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('items');
    }
} 