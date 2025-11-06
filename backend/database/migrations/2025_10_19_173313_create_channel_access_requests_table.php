<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('channel_access_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained('community_channels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('message')->nullable(); // Optional message from user
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['channel_id', 'user_id']);
        });

        // Add is_private column to channels
        Schema::table('community_channels', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('type');
        });
    }

    public function down()
    {
        Schema::table('community_channels', function (Blueprint $table) {
            $table->dropColumn('is_private');
        });
        
        Schema::dropIfExists('channel_access_requests');
    }
};