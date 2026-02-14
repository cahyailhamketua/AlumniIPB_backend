<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToJobOpeningsTable extends Migration
{
    public function up()
    {
        Schema::table('job_openings', function (Blueprint $table) {
            $table->index('industry', 'idx_job_openings_industry');
            $table->index('position', 'idx_job_openings_position');
            $table->index('deadline', 'idx_job_openings_deadline');
            $table->index('active', 'idx_job_openings_active');
            $table->index('created_at', 'idx_job_openings_created_at');
        });
    }

    public function down()
    {
        Schema::table('job_openings', function (Blueprint $table) {
            $table->dropIndex('idx_job_openings_industry');
            $table->dropIndex('idx_job_openings_position');
            $table->dropIndex('idx_job_openings_deadline');
            $table->dropIndex('idx_job_openings_active');
            $table->dropIndex('idx_job_openings_created_at');
        });
    }
}
