<?php

namespace Seatplus\Notifications\Notifications;

use Carbon\Carbon;
use Illuminate\Notifications\Notification;

abstract class NewEveMail extends Notification implements DescribeNotificationInterface
{

    public function __construct(
        public int $sender_id,
        public string $sender_name,
        public string $subject,
        public Carbon $timestamp,
        public string $route
    )
    {
        //
    }

    abstract public function via(mixed $notifiable): array;

}
