<?php


namespace Seatplus\Notifications\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Notifications\Models\Subscription;

class NotificationsController
{
    use ValidatesRequests;

    public function index()
    {
        $route = head(data_get(config('notification.channels'), '*.route'));

        return Redirect::route($route);
    }

    public function affiliatedCharacters(Request $request)
    {

        $class = $this->getNotificationClass($request);

        if($class::getCorporationRole()) {
            return [];
        }

        return $this->getIdsFromFlavour('character', $this->getAffiliatedIds($request));
    }

    public function affiliatedCorporations(Request $request)
    {

        return $this->getIdsFromFlavour('corporation', $this->getAffiliatedIds($request));
    }

    public function affiliatedAlliances(Request $request)
    {

        return $this->getIdsFromFlavour('alliance', $this->getAffiliatedIds($request));
    }

    public function currentSubscription(Request $request)
    {
        $validated_data = $request->validate([
            'notification' => ['required', 'string'],
            'notifiable_id' => ['required', 'numeric'],
            'notifiable_type' => ['required', 'string'],
        ]);

        $subscription = Subscription::query()->firstWhere([
            'notification' => data_get($validated_data, 'notification'),
            'notifiable_id' => data_get($validated_data, 'notifiable_id'),
            'notifiable_type' => data_get($validated_data, 'notifiable_type'),
        ]);

        if (is_null($subscription)) {
            return [
                'character_ids' => [],
                'corporation_ids' => [],
                'alliance_ids' => [],
            ];
        }

        return $subscription->affiliated_entities;
    }

    public function subscribe(Request $request)
    {
        $validated_data = $request->validate([
            'notification' => ['required', 'string'],
            'notifiable_id' => ['required', 'numeric'],
            'notifiable_type' => ['required', 'string'],
            'affiliated_entities' => ['required', 'array', function ($attribute, $value, $fail) use ($request) {
                $ids = [
                    ...data_get($value, 'character_ids'),
                    ...data_get($value, 'corporation_ids'),
                    ...data_get($value, 'alliance_ids'),
                ];

                $notification = $request->get('notification');
                $affiliated_ids = getAffiliatedIdsByPermission($notification::getPermission(), $notification::getCorporationRole());

                if (collect($ids)->intersect($affiliated_ids)->isEmpty()) {
                    $fail("You try to subscribe to entities which are not affiliated to you");
                }
            }],
        ]);

        Subscription::updateOrCreate([
            'notification' => data_get($validated_data, 'notification'),
            'notifiable_id' => data_get($validated_data, 'notifiable_id'),
            'notifiable_type' => data_get($validated_data, 'notifiable_type'),
        ], [
            'affiliated_entities' => data_get($validated_data, 'affiliated_entities'),
        ]);

        return redirect()->back();
    }

    private function getAffiliatedIds(Request $request) : array
    {
        $class = $this->getNotificationClass($request);

        return getAffiliatedIdsByPermission($class::getPermission(), $class::getCorporationRole());
    }

    private function getNotificationClass(Request $request) : string
    {
        $validated_data = $request->validate([
            'notification' => ['required', 'string'],
        ]);

        $class = data_get($validated_data, 'notification');

        return $class;
    }

    private function getIdsFromFlavour(string $flavour, array $affiliated_ids) : Collection
    {
        $builder = $this->getBuilderFromFlavour($flavour);

        return $builder->select("{$flavour}_id")
            ->whereIn("{$flavour}_id", $affiliated_ids)
            ->pluck("{$flavour}_id");

    }

    private function getBuilderFromFlavour(string $flavour): Builder
    {
        return match ($flavour) {
            'character' => CharacterInfo::query(),
            'corporation' => CorporationInfo::query(),
            'alliance' => AllianceInfo::query(),
        };
    }
}
