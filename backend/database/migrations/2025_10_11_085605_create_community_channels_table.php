<?php
// database/migrations/xxxx_xx_xx_create_community_channels_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('community_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('type', ['announcement', 'general', 'restricted'])->default('general');
            $table->integer('position')->default(0);
            $table->timestamps();
            
            $table->unique(['community_id', 'slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('community_channels');
    }
};