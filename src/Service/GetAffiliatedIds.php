<?php


namespace Seatplus\Notifications\Service;

use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

class GetAffiliatedIds
{
    protected Collection $ids;

    public function __construct()
    {
        $this->ids = collect();
    }

    public function get(array $affiliated_ids) : Collection
    {
        CharacterInfo::whereIn('character_id', data_get($affiliated_ids, 'character_ids'))
            ->pluck('character_id')
            ->each(fn ($character_id) => $this->ids->push($character_id));

        CorporationInfo::query()
            ->with('characters')
            ->whereIn('corporation_id', data_get($affiliated_ids, 'corporation_ids'))
            ->get()
            ->each(fn ($corporation) => $this->ids->push([$corporation->corporation_id, ...$corporation->characters->pluck('character_id')]));

        AllianceInfo::query()
            ->with('corporations', 'characters')
            ->whereIn('alliance_id', data_get($affiliated_ids, 'alliance_ids'))
            ->get()
            ->each(fn ($alliance) => $this->ids->push([
                $alliance->alliance_id,
                ...$alliance->corporations->pluck('corporation_id'),
                ...$alliance->characters->pluck('character_id'),
            ]));

        return $this->ids->flatten()->unique();
    }
}
