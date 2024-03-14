<?php

namespace App\Livewire\Cms;

use Exception;
use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Enums\Cms\CommentStatusEnum;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class Comment extends Component
{
    use UsesSpamProtection;

    public $comment;
    public ?int $parentId;
    public ?int $postId;
    public HoneypotData $extraFields;

    public function mount()
    {
        $this->extraFields = new HoneypotData();
    }

    #[Validate('required|string|min:5|max:1000')]
    public $newComment = "";

    public function sendComment()
    {
        try{
            $this->protectAgainstSpam();
        } catch (Exception $e)
        {
            Log::error("Spam detected",$e->getMessage());
        }

        try{
            $this->validate();

            $userId = auth() ? auth()->id() : null;

            if($this->postId)
            {
                Post::find($this->postId)->comments()->create([
                    'user_id' => $userId,
                    'body' => $this->newComment,
                    'status' => CommentStatusEnum::AWAITING_MODERATION,
                ]);
            } else {
                $this->comment->post->comments()->create([
                    'user_id' => $userId,
                    'body' => $this->newComment,
                    'status' => CommentStatusEnum::AWAITING_MODERATION,
                    'parent_id' => $this->parentId,
                ]);
            }

            $this->reset('newComment');
        }catch(Exception $e)
        {
            Log::error("Cannot insert comment",$e->getMessage());
        }

    }

    public function render()
    {
        return view('livewire.cms.comment');
    }
}
