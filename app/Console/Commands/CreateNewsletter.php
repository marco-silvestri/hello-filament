<?php

namespace App\Console\Commands;

use App\Enums\Cms\InternalNewsletterStatusEnum;
use App\Enums\Cms\PostStatusEnum;
use App\Models\Newsletter;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-newsletter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create newsletter core info.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('start creating newsletter');
        $lastNewsletter = Newsletter::query()->orderBy('id', 'desc')->first();

        if ($lastNewsletter) {
            if ($lastNewsletter->created_at > Carbon::now()->startOfDay()->subDays(7)) {
                return $this->info('newsletter already present, abort creation');
            } else {
                $lastNewsletterRelatedPostsId = [];
                foreach ($lastNewsletter->json_content as $block) {
                    if ($block['type'] == 'related_posts') {
                        $lastNewsletterRelatedPostsId[] = $block['data']['posts'];
                    }
                }
                $latest3Posts = Post::query()->orderBy('id', 'desc')->whereNotIn('id', $lastNewsletterRelatedPostsId)->where('status', PostStatusEnum::PUBLISH->value)->limit(3)->pluck('id')->toArray();
                
                $newsletter = Newsletter::create([
                    'name' => "Newsletter n. {$get('number')} del {$date}",
                ]);
            }
        } else {
        }
    }
}
