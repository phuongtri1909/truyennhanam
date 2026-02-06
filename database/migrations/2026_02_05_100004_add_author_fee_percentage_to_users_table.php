<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('author_fee_percentage')->nullable()->after('coins')
                ->comment('Phí nền tảng riêng cho tác giả (%). NULL = dùng mặc định từ config.');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('author_fee_percentage');
        });
    }
};
