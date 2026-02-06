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
        Schema::create('author_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('facebook_link')->comment('Đường dẫn Facebook');
            $table->string('telegram_link')->nullable()->comment('Đường dẫn Telegram');
            $table->string('other_platform')->comment('Tên nền tảng khác');
            $table->string('other_platform_link')->comment('Đường dẫn nền tảng khác');
            $table->text('introduction')->nullable()->comment('Giới thiệu tác giả');
            $table->string('status')->default('pending')->comment('Trạng thái đơn (pending, approved, rejected)');
            $table->text('admin_note')->nullable()->comment('Ghi chú của quản trị viên');
            $table->timestamp('submitted_at')->useCurrent()->comment('Ngày gửi đơn');
            $table->timestamp('reviewed_at')->nullable()->comment('Ngày xem xét đơn');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_applications');
    }
};
