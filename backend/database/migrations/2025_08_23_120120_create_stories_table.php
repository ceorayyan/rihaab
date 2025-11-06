<?php

// database/migrations/xxxx_create_stories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['image', 'video', 'text']);
            $table->text('content'); // file path for image/video, text content for text stories
            $table->text('caption')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'is_active', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stories');
    }
};
