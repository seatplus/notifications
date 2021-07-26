<?php

use Illuminate\Support\Facades\Queue;
use Seatplus\Notifications\Jobs\NewEveMail;
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

it('dispatches a helper job, whenever a new eve mail is created', function () {

    Queue::fake();

    Queue::assertNothingPushed();

    $mail = Mail::factory()
        ->has(MailRecipients::factory()->count(1)->state([
            'receivable_id' => $this->test_character->character_id,
            'receivable_type' => \Seatplus\Eveapi\Models\Character\CharacterInfo::class,
        ]), 'recipients')
        ->create([
            'from' => 1337,
        ]);

    Queue::assertPushedOn('medium', NewEveMail::class);
});

test('helper job delays notification creation if recipients are not ready', function () {

    Queue::fake();

    Queue::assertNothingPushed();

    $mail = Mail::factory()
        /*->has(MailRecipients::factory()->count(1)->state([
            'receivable_id' => $this->test_character->character_id,
            'receivable_type' => \Seatplus\Eveapi\Models\Character\CharacterInfo::class,
        ]), 'recipients')*/
        ->create([
            'from' => 1337,
        ]);

    $job = Mockery::mock(NewEveMail::class,[$mail])->makePartial();
    $job->shouldReceive('release')
        ->once();

    $job->handle();
});

it('creates an outbox entry if recipients are ready', function () {
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

    Queue::fake();

    Queue::assertNothingPushed();

    $mail = Mail::factory()
        ->has(MailRecipients::factory()->count(1)->state([
            'receivable_id' => $this->test_character->character_id,
            'receivable_type' => \Seatplus\Eveapi\Models\Character\CharacterInfo::class,
        ]), 'recipients')
        ->create([
            'from' => 1337,
        ]);

    // simulate cached name result for from_id
    cache()->set('name:1337', ['name' => 'LeetName']);

    $job = new NewEveMail($mail);

    expect($mail->recipients->isEmpty())->toBeFalse();

    $job->handle();

    expect(\Seatplus\Notifications\Models\Outbox::all())->toHaveCount(1);
});
