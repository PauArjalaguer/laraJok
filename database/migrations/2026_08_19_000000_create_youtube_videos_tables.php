<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['channel', 'playlist'])->default('channel');
            $table->string('identifier'); // @handle or playlist_id
            $table->string('channel_id')->nullable(); // UC...
            $table->string('playlist_id')->nullable(); // PL...
            $table->string('url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_channel_id')->nullable()->constrained('video_channels')->onDelete('cascade');
            $table->string('youtube_id', 30)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('thumbnail_url', 500)->nullable();
            $table->string('url', 500)->nullable();
            $table->dateTime('published_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
        Schema::dropIfExists('video_channels');
    }
};
