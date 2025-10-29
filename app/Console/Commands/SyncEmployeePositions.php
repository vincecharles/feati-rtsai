<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SyncEmployeePositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:sync-positions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync employee positions with their role labels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing employee positions...');

        $employees = User::whereHas('profile')->with(['profile', 'role'])->get();
        $updated = 0;

        foreach ($employees as $employee) {
            if ($employee->profile && $employee->role) {
                $employee->profile->update(['position' => $employee->role->label]);
                $updated++;
            }
        }

        $this->info("Successfully synced {$updated} employee positions.");
        return Command::SUCCESS;
    }
}
