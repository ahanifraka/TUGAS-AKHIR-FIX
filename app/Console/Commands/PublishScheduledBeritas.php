<?php

namespace App\Console\Commands;

use App\Models\Berita;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledBeritas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beritas:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled beritas that have reached their publication date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled beritas to publish...');

        // Find all beritas that are marked as published, have a scheduled date, and that date has passed
        $beritas = Berita::where('published', 1)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        if ($beritas->isEmpty()) {
            $this->info('No scheduled beritas to publish at this time.');
            return 0;
        }

        $count = 0;
        foreach ($beritas as $berita) {
            try {
                // The berita is already marked as published, but we log this event
                Log::info('Scheduled berita published', [
                    'berita_id' => $berita->id,
                    'title' => $berita->title,
                    'published_at' => $berita->published_at,
                ]);

                // Create activity log
                \App\Models\ActivityLog::create([
                    'user_id' => $berita->created_by,
                    'activity' => 'Scheduled berita published: ' . $berita->title,
                ]);

                $count++;
                $this->info("Published: {$berita->title}");
            } catch (\Exception $e) {
                Log::error('Error publishing scheduled berita', [
                    'berita_id' => $berita->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to publish: {$berita->title}");
            }
        }

        $this->info("Successfully processed {$count} scheduled beritas.");
        return 0;
    }
}
