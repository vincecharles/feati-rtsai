<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncStudentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:sync-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync student data from student_profiles to users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting student data synchronization...');

        try {
            DB::beginTransaction();

            $students = User::with('studentProfile')
                ->whereHas('role', function($q) {
                    $q->where('name', 'student');
                })
                ->get();

            $this->info("Found {$students->count()} students to process.");

            $updated = 0;
            $skipped = 0;
            $bar = $this->output->createProgressBar($students->count());

            foreach ($students as $student) {
                if ($student->studentProfile && $student->studentProfile->student_number) {
                    $student->update([
                        'student_id' => $student->studentProfile->student_number,
                        'program' => $student->studentProfile->course ?? $student->studentProfile->program,
                        'year_level' => $student->studentProfile->year_level,
                    ]);
                    $updated++;
                } else {
                    $skipped++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            DB::commit();

            $this->info("✓ Synchronization completed successfully!");
            $this->table(
                ['Status', 'Count'],
                [
                    ['Updated', $updated],
                    ['Skipped (no profile)', $skipped],
                    ['Total', $students->count()],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("✗ Failed to sync student data: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
