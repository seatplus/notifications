<?php

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->test_user->givePermissionTo('superuser');

    expect($this->test_user->can('superuser'))->toBeTrue();
});

it('has character affiliations for notifications', function () {

    $response = actingAs($this->test_user)
        ->post(route('notification.affiliated.characters'), [
            'notification' => \Seatplus\Notifications\Tests\Stubs\TestEveMail::class
        ]);

    expect($response->original)->toEqual(\Seatplus\Eveapi\Models\Character\CharacterInfo::pluck('character_id'));
});

it('has corporation affiliations for notifications', function () {

    $response = actingAs($this->test_user)
        ->post(route('notification.affiliated.corporations'), [
            'notification' => \Seatplus\Notifications\Tests\Stubs\TestEveMail::class
        ]);

    expect($response->original)->toEqual(\Seatplus\Eveapi\Models\Corporation\CorporationInfo::pluck('corporation_id'));
});
