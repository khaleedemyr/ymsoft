<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('floor_orders', function (Blueprint $table) {
            // Hapus kolom outlet_id jika ada
            if (Schema::hasColumn('floor_orders', 'outlet_id')) {
                $table->dropForeign(['outlet_id']);
                $table->dropColumn('outlet_id');
            }
            
            // Tambah kolom warehouse_id
            $table->unsignedBigInteger('warehouse_id')->after('fo_number');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    public function down()
    {
        Schema::table('floor_orders', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
            
            // Kembalikan kolom outlet_id jika perlu
            $table->unsignedBigInteger('outlet_id');
            $table->foreign('outlet_id')->references('id_outlet')->on('tbl_data_outlet');
        });
    }
}; 