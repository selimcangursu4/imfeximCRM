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
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete()->after('address');
            $table->decimal('deal_value', 10, 2)->nullable()->default(0)->after('status');
            $table->string('source')->nullable()->after('deal_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn(['assigned_user_id', 'deal_value', 'source']);
        });
    }
};
