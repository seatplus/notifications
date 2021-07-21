<?php


namespace Seatplus\Notifications\Listeners;

use Illuminate\Support\Collection;
use ReflectionClass;
use Seatplus\Eveapi\Events\EveMailCreated;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Notifications\Models\Outbox;
use Seatplus\Notifications\Models\Subscription;
use Seatplus\Notifications\Notifications\NewEveMail;
use Seatplus\Web\Services\GetNamesFromIdsService;

class EveMailListener
{
    public function handle(EveMailCreated $event)
    {
        $mail = Mail::findOrFail($event->mail_id);

        $this->created($mail);
    }

    public function created(Mail $mail)
    {
        $subscriptions = Subscription::cursor()->filter(function ($subscription) {
            $reflector = new ReflectionClass($subscription->notification);

            return $reflector->isSubclassOf(NewEveMail::class);
        })->filter(function (Subscription $subscription) use ($mail) {
            return $mail->recipients->pluck('receivable_id')
                ->intersect($this->getAffiliatedIds($subscription->affiliated_entities))
                ->isNotEmpty();
        });

        $sender_name = data_get((new GetNamesFromIdsService)->execute([$mail->from])->first(), 'name');

        foreach ($subscriptions as $subscription) {
            $notification = $subscription->notification;
            $notification = new $notification(
                $mail->from,
                $sender_name,
                $mail->subject,
                carbon($mail->timestamp),
                route('character.mails'),
            );

            Outbox::create([
                'notifiable_type' => $subscription->notifiable_type,
                'notifiable_id' => $subscription->notifiable_id,
                'notification' => serialize($notification),
                'is_send' => false,
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
