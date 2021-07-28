<?php


namespace Seatplus\Notifications\Service;


use ReflectionClass;
use Seatplus\Notifications\Models\Outbox;
use Seatplus\Notifications\Models\Subscription;

class CreateOutboxEntriesFromSubscription
{

    protected GetAffiliatedIds $getAffiliatedIds;

    public function __construct(
        protected string $notification_class
    )
    {
        $this->getAffiliatedIds = new GetAffiliatedIds;
    }

    public function handle(array $relevant_ids, array $constructor_array)
    {
        Subscription::cursor()
            // only handle subscriptions of given notification_class
            ->filter(function ($subscription) {
                $reflector = new ReflectionClass($subscription->notification);

                return $reflector->isSubclassOf($this->notification_class);
            })
            // filter non relevant subscriptions of affiliated entities
            ->filter(fn (Subscription $subscription) => collect($relevant_ids)
                ->intersect($this->getAffiliatedIds->get($subscription->affiliated_entities))
                ->isNotEmpty()
            )
            // create a subscription for the remaining subscription
            ->each(function(Subscription $subscription) use ($constructor_array) {
            $notification = $subscription->notification;
            $notification = new $notification(...$constructor_array);

            Outbox::create([
                'notifiable_type' => $subscription->notifiable_type,
                'notifiable_id' => $subscription->notifiable_id,
                'notification' => $notification,
                'is_sent' => false,
            ]);
        });
    }
}