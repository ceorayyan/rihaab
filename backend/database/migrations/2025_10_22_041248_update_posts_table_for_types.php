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
       Schema::table('posts', function (Blueprint $table) {
    $table->string('type')->default('standard')->after('content'); // standard, poll, qa
    $table->string('privacy')->default('public')->after('type'); // public, friends, private
    $table->json('metadata')->nullable()->after('privacy'); // for storing additional data
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
