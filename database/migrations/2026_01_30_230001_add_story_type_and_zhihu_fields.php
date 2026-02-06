<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->string('story_type', 20)->default('normal')->after('status')
                ->comment('normal: truyện thường, zhihu: truyện zhihu (có quảng cáo affiliate)');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_publish_zhihu')->default(false)->after('role')
                ->comment('Quyền đăng truyện zhihu do admin cấp');
        });
    }

    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn('story_type');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('can_publish_zhihu');
        });
    }
};
