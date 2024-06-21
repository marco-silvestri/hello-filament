<?php

namespace App\Jobs;

use App\Models\Communication;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Mail\Cms\PostHasBeenPublished;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Communication $communication
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->communication->contacts
            ->map(function ($contact) {
                $mail = (new PostHasBeenPublished($this->communication));
                $mailable = new Mail();
                $mailable::to(
                    $contact->email,
                    $contact->name
                )->send($mail);
            });
    }
}
