<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->id();
            $table->string('url')->comment('Link affiliate Shopee');
            $table->string('banner_path')->nullable()->comment('Đường dẫn ảnh banner');
            $table->string('title')->nullable()->comment('Tiêu đề mô tả');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_links');
    }
};
