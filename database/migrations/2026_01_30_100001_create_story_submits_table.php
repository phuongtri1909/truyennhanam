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
        Schema::create('story_submits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->text('submitted_note')->nullable()->comment('Ghi chú tác giả khi gửi');
            $table->timestamp('submitted_at')->useCurrent()->comment('Thời điểm gửi');
            $table->text('admin_note')->nullable()->comment('Ghi chú admin khi duyệt/từ chối');
            $table->timestamp('reviewed_at')->nullable()->comment('Thời điểm admin xử lý');
            $table->enum('result', ['pending', 'approved', 'rejected'])->default('pending')->comment('Kết quả: chờ/duyệt/từ chối');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_submits');
    }
};
