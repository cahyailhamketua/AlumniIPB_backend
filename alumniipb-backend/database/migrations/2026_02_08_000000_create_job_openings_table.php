<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobOpeningsTable extends Migration
{
    public function up()
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->text('description')->nullable();
            $table->string('industry')->nullable();
            $table->string('company')->nullable();
            $table->enum('type', ['internship', 'job'])->default('job');
            $table->dateTime('deadline')->nullable();
            $table->string('location')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('salary_min')->nullable();
            $table->unsignedBigInteger('salary_max')->nullable();
            $table->json('requirements')->nullable();
            $table->string('link');
            $table->boolean('active')->default(false);
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('created_by_type')->nullable();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_openings');
    }
}
