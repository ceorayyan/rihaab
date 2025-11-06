<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('channel_moderators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('community_channels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['channel_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('channel_moderators');
    }
};