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
        Schema::create('files_convert', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('file_id')->constrained('files')->onDelete('cascade');
            $table->enum('status', ['PENDING', 'CONVERTING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->tinyInteger('progress')->default(0);
            $table->text('message')->nullable();
            $table->unsignedInteger('job_id')->nullable();
            $table->tinyText('to_extension');
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files_convert');
    }
};
