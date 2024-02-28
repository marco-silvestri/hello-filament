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
                $latest3Posts = Post::query()->orderBy('id', 'desc')->whereNotIn('id', $lastNewsletterRelatedPostsId)->where('status', PostStatusEnum::PUBLISH->value)->limit(3)->get();
                $number = $lastNewsletter->number + 1;
                $date = Carbon::now()->format('d/m/Y');
                $jsonContent = $this->buildContent($latest3Posts);
                $newsletter = Newsletter::create([
                    'name' => "Newsletter n. {$number} del {$date}",
                    'subject' => "AudioFader news",
                    'number' => $number,
                    'status' => InternalNewsletterStatusEnum::DRAFT->value,
                    'json_content' => $jsonContent
                ]);

                if ($newsletter) {
                    return $this->info('newsletter created with id: ' . $newsletter->id);
                }
            }
        } else {
            $posts = Post::query()->orderBy('id', 'desc')->where('status', PostStatusEnum::PUBLISH->value)->limit(3)->get();
            $number = 1;
            $date = Carbon::now()->format('d/m/Y');
            $jsonContent = $this->buildContent($posts);
            $newsletter = Newsletter::create([
                'name' => "Newsletter n. {$number} del {$date}",
                'subject' => "AudioFader news",
                'number' => $number,
                'status' => InternalNewsletterStatusEnum::DRAFT->value,
                'json_content' => $jsonContent
            ]);

            if ($newsletter) {
                return $this->info('newsletter created with id: ' . $newsletter->id);
            }
        }
    }

    private function buildContent($posts)
    {
        $blocks = [];
        foreach ($posts as $post) {
            $blocks[] = [
                'data' => [
                    'posts' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'alignment' => 'center',
                ],
                'type' => 'related_posts'
            ];
        }

        return $blocks;
    }
}
