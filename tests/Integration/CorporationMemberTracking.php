<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use function Pest\Laravel\actingAs;

beforeEach(function () {

    $this->test_user->givePermissionTo('superuser');

    expect($this->test_user->can('superuser'))->toBeTrue();

});

it('a new member triggers a new createOutboxEntries event', function () {

    class NewMember extends \Seatplus\Notifications\Notifications\NewCorporationMember
    {

        public static function getIcon(): string
        {
            // TODO: Implement getIcon() method.
        }

        public static function getTitle(): string
        {
            // TODO: Implement getTitle() method.
        }

        public static function getDescription(): string
        {
            // TODO: Implement getDescription() method.
        }

        public static function getPermission(): string
        {
            return '';
        }

        public static function getCorporationRole(): string
        {
            return  '';
        }

        public function via(mixed $notifiable): array
        {
            // TODO: Implement via() method.
        }
    }

    $response = actingAs($this->test_user)->post(route('notification.subscribe'), [
        'notification' => NewMember::class,
        'notifiable_id' => $this->test_user->id,
        'notifiable_type' => \Seatplus\Auth\Models\User::class,
        'affiliated_entities' => [
            'character_ids' => [],
            'corporation_ids' => [$this->test_character->corporation->corporation_id],
            'alliance_ids' => [],
        ],
    ])->assertRedirect();

    expect(\Seatplus\Notifications\Models\Subscription::all())->toHaveCount(1);

    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(0);

    CorporationMemberTracking::factory()
        ->create([
            'character_id' => $this->test_character->character_id,
            'corporation_id' => $this->test_character->corporation->corporation_id,
            'start_date' => now()->subDay()
        ]);

    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(1);
});

it('a leaving member triggers a new createOutboxEntries event', function () {

    class LeavingMember extends \Seatplus\Notifications\Notifications\DeleteCorporationMember
    {

        public static function getIcon(): string
        {
            // TODO: Implement getIcon() method.
        }

        public static function getTitle(): string
        {
            // TODO: Implement getTitle() method.
        }

        public static function getDescription(): string
        {
            // TODO: Implement getDescription() method.
        }

        public static function getPermission(): string
        {
            return '';
        }

        public static function getCorporationRole(): string
        {
            return  '';
        }

        public function via(mixed $notifiable): array
        {
            // TODO: Implement via() method.
        }
    }

    $response = actingAs($this->test_user)->post(route('notification.subscribe'), [
        'notification' => LeavingMember::class,
        'notifiable_id' => $this->test_user->id,
        'notifiable_type' => \Seatplus\Auth\Models\User::class,
        'affiliated_entities' => [
            'character_ids' => [],
            'corporation_ids' => [$this->test_character->corporation->corporation_id],
            'alliance_ids' => [],
        ],
    ])->assertRedirect();

    expect(\Seatplus\Notifications\Models\Subscription::all())->toHaveCount(1);

    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(0);

    Event::fakeFor(fn() => CorporationMemberTracking::factory()
        ->create([
            'character_id' => $this->test_character->character_id,
            'corporation_id' => $this->test_character->corporation->corporation_id,
            'start_date' => now()->subDay()
        ])
    );

    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(0);
    expect(CorporationMemberTracking::all())->toHaveCount(1);

    CorporationMemberTracking::first()->delete();
    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(1);


});