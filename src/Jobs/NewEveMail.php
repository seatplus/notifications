<?php


namespace Seatplus\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Notifications\Service\CreateOutboxEntriesFromSubscription;
use Seatplus\Web\Services\GetNamesFromIdsService;

class NewEveMail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected CreateOutboxEntriesFromSubscription $createOutboxEntriesFromSubscription;

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addHours(12);
    }

    public function __construct(
        protected Mail $mail,
    ) {
        $this->createOutboxEntriesFromSubscription = new CreateOutboxEntriesFromSubscription(
            \Seatplus\Notifications\Notifications\NewEveMail::class
        );
    }

    public function handle()
    {
        if ($this->mail->recipients->isEmpty()) {
            $this->release(30);
        } else {
            $constructor_array = [
                $this->mail->from,
                data_get((new GetNamesFromIdsService)->execute([$this->mail->from])->first(), 'name'),
                $this->mail->subject,
                carbon($this->mail->timestamp),
                route('character.mails'),
            ];

            $this->createOutboxEntriesFromSubscription->handle(
                $this->mail->recipients->pluck('receivable_id')->toArray(),
                $constructor_array
            );
        }
    }
}
