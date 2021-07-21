<?php

use function Pest\Laravel\actingAs;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Mail\MailRecipients;

it('redirects to first notification package', function () {
    //$user = $this->test_user;

    config()->set(
        key: "notification.channels",
        value: [
            [
                'name' => 'Test Channel',
                'route' => 'auth.login',
            ],
        ]
    );

    actingAs($this->test_user)->get(route('notifications.index'))->assertRedirect();
});

test('one can subscribe to new eve mail notifications', function () {



    // first check that no subscription is present
    actingAs($this->test_user)->post(route('notification.current.subscription'), [
        'notification' => \Seatplus\Notifications\Tests\Stubs\TestEveMail::class,
        'notifiable_id' => $this->test_user->id,
        'notifiable_type' => \Seatplus\Auth\Models\User::class,
    ])->assertJson([
        'character_ids' => [],
        'corporation_ids' => [],
        'alliance_ids' => [],
    ]);

    expect(\Seatplus\Notifications\Models\Subscription::all())->toHaveCount(0);

    $response = actingAs($this->test_user)->post(route('notification.subscribe'), [
        'notification' => \Seatplus\Notifications\Tests\Stubs\TestEveMail::class,
        'notifiable_id' => $this->test_user->id,
        'notifiable_type' => \Seatplus\Auth\Models\User::class,
        'affiliated_entities' => [
            'character_ids' => [$this->test_character->character_id],
            'corporation_ids' => [],
            'alliance_ids' => [],
        ],
    ])->assertRedirect();

    expect(\Seatplus\Notifications\Models\Subscription::all())->toHaveCount(1);

    actingAs($this->test_user)->post(route('notification.current.subscription'), [
        'notification' => \Seatplus\Notifications\Tests\Stubs\TestEveMail::class,
        'notifiable_id' => $this->test_user->id,
        'notifiable_type' => \Seatplus\Auth\Models\User::class,
    ])->assertJson([
        'character_ids' => [$this->test_character->character_id],
        'corporation_ids' => [],
        'alliance_ids' => [],
    ]);
});

test('creates an outbox entry for subscribed entities, whenever a new eve mail is created', function () {
    $response = actingAs($this->test_user)->post(route('notification.subscribe'), [
        'notification' => \Seatplus\Notifications\Tests\Stubs\TestEveMail::class,
        'notifiable_id' => $this->test_user->id,
        'notifiable_type' => \Seatplus\Auth\Models\User::class,
        'affiliated_entities' => [
            'character_ids' => [$this->test_character->character_id],
            'corporation_ids' => [],
            'alliance_ids' => [],
        ],
    ])->assertRedirect();

    // simulate cached name result for from_id
    cache()->set('name:1337', ['name' => 'LeetName']);

    $mail = Mail::factory()
        ->has(MailRecipients::factory()->count(1)->state([
            'receivable_id' => $this->test_character->character_id,
            'receivable_type' => \Seatplus\Eveapi\Models\Character\CharacterInfo::class,
        ]), 'recipients')
        ->create([
            'from' => 1337,
        ]);

    event(new \Seatplus\Eveapi\Events\EveMailCreated($mail->id));

    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(1);
});
