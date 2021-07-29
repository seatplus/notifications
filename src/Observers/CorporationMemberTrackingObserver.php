<?php


namespace Seatplus\Notifications\Observers;


use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Notifications\Notifications\NewCorporationMember;
use Seatplus\Notifications\Service\CreateOutboxEntriesFromSubscription;
use Seatplus\TelegramChannel\Notifications\RemoveCorporationMember;
use Seatplus\Web\Services\GetNamesFromIdsService;

class CorporationMemberTrackingObserver
{
    public function created(CorporationMemberTracking $tracking)
    {

        $constructor_array = [
            CorporationInfo::find($tracking->corporation_id)?->name ?? $tracking->corporation_id,
            CharacterInfo::find($tracking->character_id)?->name ?? data_get((new GetNamesFromIdsService)->execute([$tracking->character_id])->first(), 'name'),
            $tracking->start_date
        ];

        (new CreateOutboxEntriesFromSubscription(NewCorporationMember::class))
            ->handle($tracking->corporation_id, $constructor_array);
    }

    private function deleted(CorporationMemberTracking $tracking)
    {
        $constructor_array = [
            CorporationInfo::find($tracking->corporation_id)?->name ?? $tracking->corporation_id,
            CharacterInfo::find($tracking->character_id)?->name ?? data_get((new GetNamesFromIdsService)->execute([$tracking->character_id])->first(), 'name'),
            $tracking->updated_at
        ];

        (new CreateOutboxEntriesFromSubscription(RemoveCorporationMember::class))
            ->handle($tracking->corporation_id, $constructor_array);
    }

}