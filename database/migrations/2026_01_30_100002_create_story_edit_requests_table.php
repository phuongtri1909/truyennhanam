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
        Schema::create('story_edit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug');
            $table->text('description');
            $table->string('cover')->nullable();
            $table->string('cover_thumbnail')->nullable();
            $table->string('author_name')->nullable();
            $table->text('categories_data')->nullable()->comment('JSON array category ids');
            $table->boolean('is_18_plus')->default(false);
            $table->boolean('has_combo')->default(false);
            $table->integer('combo_price')->nullable()->default(0);
            $table->text('review_note')->nullable()->comment('Ghi chú tác giả khi gửi');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_edit_requests');
    }
};
