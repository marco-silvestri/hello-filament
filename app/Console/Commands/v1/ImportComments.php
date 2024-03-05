<?php

namespace App\Console\Commands\v1;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Traits\Cms\HasWpData;
use Illuminate\Console\Command;
use App\Enums\Cms\CommentStatusEnum;

class ImportComments extends Command
{
    use HasWpData;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $commentsSource = [
                'key' => 'wp_comments',
                'source' => 'legacy-data/audio_fader_comments.json',
        ];

        $rawComments = $this->collectWpJson($commentsSource['source'], $commentsSource['key']);

        $bar = $this->output->createProgressBar(count($rawComments));
        $bar->setFormat('debug');
        $bar->start();

        $rawComments->map(function($rawComment) use(&$bar){
            $user = User::where('email', $rawComment->comment_author_email)->first();
            $post = Post::where('legacy_id', $rawComment->comment_post_ID)->first();

            if($post)
            {
                $postId = $post->id;
                $data = [
                    'body' => $rawComment->comment_content,
                    'post_id' => $postId,
                    'status' => CommentStatusEnum::APPROVED->getValue(),
                    'created_at' => Carbon::parse($rawComment->comment_date),
                ];

                if($user)
                {
                    $data['author_id'] =  $user->id;
                }

                Comment::firstOrCreate([
                    'body' => $data['body'],
                ],$data
                );
            }else{
                $this->line("{$rawComment->comment_post_ID} not found");
            }

            $bar->advance();
        });

        $bar->finish();
        return Command::SUCCESS;
    }
}
