<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('story_ownership_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('transferred_by_id')->constrained('users')->onDelete('cascade');
            $table->string('transfer_type')->comment('ownership_change|co_owner_added|co_owner_removed');
            $table->foreignId('affected_user_id')->nullable()->constrained('users')->nullOnDelete()->comment('For co_owner_added/removed');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['story_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('story_ownership_transfers');
    }
};
