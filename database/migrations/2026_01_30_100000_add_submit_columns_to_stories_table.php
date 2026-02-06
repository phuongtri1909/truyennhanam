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
        Schema::table('stories', function (Blueprint $table) {
            $table->text('submitted_note')->nullable()->after('status')->comment('Ghi chú tác giả khi gửi duyệt');
            $table->timestamp('submitted_at')->nullable()->after('submitted_note')->comment('Thời điểm gửi duyệt');
            $table->text('admin_note')->nullable()->after('submitted_at')->comment('Ghi chú admin khi duyệt/từ chối');
            $table->timestamp('reviewed_at')->nullable()->after('admin_note')->comment('Thời điểm admin duyệt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn(['submitted_note', 'submitted_at', 'admin_note', 'reviewed_at']);
        });
    }
};
