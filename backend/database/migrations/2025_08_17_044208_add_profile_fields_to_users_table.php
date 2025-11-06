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
            $table->string('username')->unique()->after('name');
            $table->string('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->date('dob')->nullable();
            $table->string('marital_status', 50)->nullable();
            $table->string('education', 100)->nullable();
            $table->string('occupation', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'profile_picture',
                'bio',
                'dob',
                'marital_status',
                'education',
                'occupation',
            ]);
        });
    }
};
