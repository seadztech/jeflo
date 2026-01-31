<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stockins_id');
            $table->integer('quantity');
            $table->integer('actionBy');
            $table->string('changeType');
            $table->enum('reason', ['spoilage', 'sale', 'drawings', 'returnToSupplier', 'fromSupplier']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_changes');
    }
};
