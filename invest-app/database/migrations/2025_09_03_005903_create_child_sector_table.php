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
        Schema::create('child_sectors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sector_id');
            $table->string('name');
            $table->json('data')->nullable()->change();
            $table->json('data_template')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('sector_id')
                  ->references('id')
                  ->on('sectors')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_sectors');
    }
};
