<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishScheduledArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish articles that are scheduled for current time or past';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Article::where('status', 'draft')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->update(['status' => 'published']);

        if ($count > 0) {
            $this->info("Successfully published {$count} scheduled article(s).");
        } else {
            $this->info('No scheduled articles to publish.');
        }
    }
}
