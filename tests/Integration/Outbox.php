<?php

use Seatplus\Auth\Models\User;
use Seatplus\Notifications\Models\Outbox;
use Seatplus\Notifications\Tests\Stubs\TestEveMail;

beforeEach(function () {
    $notification = new TestEveMail(
        1337,
        'TestName',
        'test-subject',
        carbon()->subDay(),
        route('character.mails')
    );

    Outbox::create([
        'notifiable_type' => User::class,
        'notifiable_id' => $this->test_user->id,
        'notification' => $notification,
        'is_sent' => false,
    ]);
});

it('it has notifiable relationship defined', function () {
    expect(Outbox::first()->notifiable instanceof User)->toBeTrue();
});

it('it has mutators for accessing and storing notifications', function () {
    expect(Outbox::first()->notification instanceof TestEveMail)->toBeTrue();
});
