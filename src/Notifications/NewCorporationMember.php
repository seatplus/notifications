<?php

namespace Seatplus\Notifications\Notifications;

use Illuminate\Notifications\Notification;

abstract class NewCorporationMember extends Notification implements DescribeNotificationInterface
{
    public function __construct(
        public string $corporation,
        public string $character,
        public string $start_date,
    ) {
        //
    }

    abstract public function via(mixed $notifiable): array;
}
