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
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->text('history')->nullable();
            $table->text('timeline')->nullable();
            $table->text('mission_focus')->nullable();
            $table->string('contact')->nullable();
            $table->string('address')->nullable();
            $table->string('gmail')->nullable();
            $table->string('instagram')->nullable();
            $table->json('organizational_structure')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
