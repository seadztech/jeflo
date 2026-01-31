<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stockins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id');
            $table->string('batch_id')->nullable();
            $table->foreignId('branch_id');
            $table->foreignId('received_by');
            $table->integer('quantity');
            $table->date('expiry_date');
            $table->string('supplier')->nullable();
            $table->text('additional_info');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockins');
    }
};
