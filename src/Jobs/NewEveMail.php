<?php


namespace Seatplus\Notifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use ReflectionClass;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Notifications\Models\Outbox;
use Seatplus\Notifications\Models\Subscription;
use Seatplus\Web\Services\GetNamesFromIdsService;

class NewEveMail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        protected Mail $mail
    ) {
    }

    public function handle()
    {
        if ($this->mail->recipients->isEmpty()) {
            return $this->release(30);
        }

        $subscriptions = Subscription::cursor()->filter(function ($subscription) {
            $reflector = new ReflectionClass($subscription->notification);

            return $reflector->isSubclassOf(\Seatplus\Notifications\Notifications\NewEveMail::class);
        })->filter(function (Subscription $subscription) {
            return $this->mail->recipients->pluck('receivable_id')
                ->intersect($this->getAffiliatedIds($subscription->affiliated_entities))
                ->isNotEmpty();
        });

        $sender_name = data_get((new GetNamesFromIdsService)->execute([$this->mail->from])->first(), 'name');

        foreach ($subscriptions as $subscription) {
            $notification = $subscription->notification;
            $notification = new $notification(
                $this->mail->from,
                $sender_name,
                $this->mail->subject,
                carbon($this->mail->timestamp),
                route('character.mails'),
            );

            Outbox::create([
                'notifiable_type' => $subscription->notifiable_type,
                'notifiable_id' => $subscription->notifiable_id,
                'notification' => $notification,
                'is_sent' => false,
            ]);
        }
    }

    private function getAffiliatedIds(array $affiliated_ids) : Collection
    {
        $ids = collect();

        CharacterInfo::whereIn('character_id', data_get($affiliated_ids, 'character_ids'))
            ->pluck('character_id')
            ->each(fn ($character_id) => $ids->push($character_id));

        CorporationInfo::query()
            ->with('characters')
            ->whereIn('corporation_id', data_get($affiliated_ids, 'corporation_ids'))
            ->get()
            ->each(fn ($corporation) => $ids->push([$corporation->corporation_id, ...$corporation->characters->pluck('character_id')]));

        AllianceInfo::query()
            ->with('corporations', 'characters')
            ->whereIn('alliance_id', data_get($affiliated_ids, 'alliance_ids'))
            ->get()
            ->each(fn ($alliance) => $ids->push([
                $alliance->alliance_id,
                ...$alliance->corporations->pluck('corporation_id'),
                ...$alliance->characters->pluck('character_id'),
            ]));

        return $ids->unique();
    }
}
