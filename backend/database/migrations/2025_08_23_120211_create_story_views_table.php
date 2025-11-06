<?php
// ===== SECOND MIGRATION FILE =====

// database/migrations/xxxx_create_story_views_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('story_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('viewer_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('viewed_at');
            $table->timestamps();
            
            $table->unique(['story_id', 'viewer_id']);
            $table->index(['story_id', 'viewed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('story_views');
    }
};