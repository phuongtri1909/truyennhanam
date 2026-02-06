<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->text('password_encrypted')->nullable()->after('price')
                ->comment('Mật khẩu mã hóa (reversible) để tác giả xem lại khi edit');
            $table->text('password_hint')->nullable()->after('password_encrypted')
                ->comment('Gợi ý mật khẩu hiển thị cho người đọc');
        });
    }

    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropColumn(['password_encrypted', 'password_hint']);
        });
    }
};
