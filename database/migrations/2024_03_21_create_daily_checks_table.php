<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_checks', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('item_id')->constrained('daily_check_items');
            $table->enum('condition', ['C', 'WM', 'D', 'NA']);
            $table->text('other_issue')->nullable();
            $table->string('checked_by');
            $table->time('time');
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite unique key untuk mencegah duplikasi check pada item yang sama di tanggal yang sama
            $table->unique(['date', 'item_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_checks');
    }
}; 