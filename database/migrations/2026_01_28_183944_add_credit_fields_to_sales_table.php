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
        Schema::table('sales', function (Blueprint $table) {
            // Replace the current payment_method enum
            // $table->enum('payment_method', ['cash', 'mpesa', 'credit', 'card', 'bank_transfer'])->default('cash')->change();
            $table->string('payment_method')->default('cash')->change();

            // Add payment status
            $table->string('payment_status')->default('pending')->after('payment_method');
            // $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->default('pending')->after('payment_method');

            // Add credit fields
            $table->decimal('amount_paid', 10, 2)->default(0)->after('total_amount');
            $table->decimal('balance_due', 10, 2)->default(0)->after('amount_paid');
            $table->date('due_date')->nullable()->after('balance_due');
            $table->timestamp('paid_at')->nullable()->after('due_date');
            $table->text('payment_notes')->nullable()->after('paid_at');


            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            //
        });
    }
};
