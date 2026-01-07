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
        Schema::table('alumnis', function (Blueprint $table) {
            $table->string('pekerjaan')->nullable()->after('angkatan');
            $table->string('perusahaan')->nullable()->after('pekerjaan');
            $table->text('alamat')->nullable()->after('perusahaan');
            $table->text('biografi')->nullable()->after('alamat');
            $table->json('riwayat_pekerjaan')->nullable()->after('biografi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnis', function (Blueprint $table) {
            $table->dropColumn([
                'pekerjaan',
                'perusahaan',
                'alamat',
                'biografi',
                'riwayat_pekerjaan',
            ]);
        });
    }
};
