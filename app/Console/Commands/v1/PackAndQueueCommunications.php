<?php

namespace App\Console\Commands\v1;

use App\Models\Communication;
use Illuminate\Console\Command;
use App\Enums\Cms\CommunicationStatusEnum;
use App\Jobs\SendMail;

class PackAndQueueCommunications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:pack-and-queue-communications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve all unsent communications and queue their mailables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $communications = Communication::with(['contacts'])
            ->where('status', CommunicationStatusEnum::SCHEDULED)
            ->where('sent_at', null)
            ->whereHas('post',function($query){
                $query->published();
            })->get();

        $communications->map(
            function (Communication $communication) {
                SendMail::dispatch($communication);
                $communication->update([
                    'status' => CommunicationStatusEnum::SENT,
                    'sent_at' => now(),
                ]);
            }
        );
    }
}
