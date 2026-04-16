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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('email');
            $table->string('phone')->nullable()->after('profile_photo');
            $table->date('birth_date')->nullable()->after('phone');
            $table->string('gender')->nullable()->after('birth_date'); // e.g., 'male', 'female', 'other'
            $table->string('role')->default('satis_danismani')->after('gender'); // admin, yonetici, web_developer, satis_danismani
            $table->string('department')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo',
                'phone',
                'birth_date',
                'gender',
                'role',
                'department',
            ]);
        });
    }
};
