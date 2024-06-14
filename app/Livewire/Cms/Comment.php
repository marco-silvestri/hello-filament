<?php

namespace App\Livewire\Cms;

use Exception;
use App\Models\Post;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Enums\Cms\CommentStatusEnum;
use App\Traits\Cms\HasMetricsTracking;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class Comment extends Component
{
    use UsesSpamProtection, HasMetricsTracking;

    public $comment;
    public ?int $parentId;
    public ?int $postId = null;
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
            session()->flash('spamDetection', __('comments.lbl-spam-detected'));
            Log::error("Spam detected",['error' => $e->getMessage()]);
        }

        try{
            $this->validate();
            $userId = auth() ? auth()->id() : null;

            $ip = $this->getIpAddress();
            $ua = $this->getUserAgent();

            $data = [
                'author_ip' => $ip,
                'user_agent' => $ua,
                'author_id' => $userId,
                'body' => $this->newComment,
                'status' => CommentStatusEnum::AWAITING_MODERATION,
            ];

            if($this->postId)
            {
                Post::find($this->postId)->comments()->create($data);
            } else {
                $data['parent_id'] = $this->parentId;
                $this->comment->post->comments()->create($data);
            }

            $this->reset('newComment');
            session()->flash('commentSuccess', __('comments.lbl-comment-success'));
        }catch(Exception $e)
        {
            session()->flash('commentFailure', __('comments.lbl-comment-failure'));
            Log::error("Cannot insert comment",['error' => $e->getMessage()]);
        }

    }

    public function render()
    {
        return view('livewire.cms.comment');
    }
}
