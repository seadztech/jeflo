<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transactions_id');
            $table->foreignId('sale_id');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('allocated_by')->nullable()->constrained('users');
            $table->timestamp('allocated_at')->useCurrent();
            $table->timestamps();
            
           
        });
    }

    public function down()
    {
        Schema::dropIfExists('allocations');
    }
};