<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JobOpening;

class DeactivateExpiredJobOpenings extends Command
{
    protected $signature = 'job:deactivate-expired';
    protected $description = 'Menonaktifkan lowongan pekerjaan yang sudah melewati deadline';

    public function handle()
    {
        $now = now();
        $affected = JobOpening::where('active', true)
            ->whereNotNull('deadline')
            ->where('deadline', '<', $now)
            ->update(['active' => false]);

        $this->info("Deactivated {$affected} expired job openings.");
        return 0;
    }
}
