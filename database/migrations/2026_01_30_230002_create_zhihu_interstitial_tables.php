<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zhihu_device_interstitials', function (Blueprint $table) {
            $table->id();
            $table->string('device_key', 100)->unique()->comment('Key thiết bị (từ cookie)');
            $table->timestamp('last_shown_at')->nullable()->comment('Lần cuối hiển thị interstitial (sau khi user click)');
            $table->timestamps();
            $table->index('last_shown_at');
        });

        Schema::create('affiliate_link_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('affiliate_link_id')->constrained()->onDelete('cascade');
            $table->string('device_key', 100)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('clicked_at')->useCurrent();
            $table->index(['story_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_link_clicks');
        Schema::dropIfExists('zhihu_device_interstitials');
    }
};
