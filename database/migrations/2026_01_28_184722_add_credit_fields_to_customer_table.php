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
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('credit_limit', 10, 2)->default(0)->after('email');
            $table->decimal('current_balance', 10, 2)->default(0)->after('credit_limit');
            $table->integer('credit_days')->default(30)->after('current_balance'); // Payment terms in days
            $table->boolean('can_buy_on_credit')->default(false)->after('credit_days');
            $table->timestamp('credit_approved_at')->nullable()->after('can_buy_on_credit');
            $table->foreignId('credit_approved_by')->nullable()->after('credit_approved_at')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
