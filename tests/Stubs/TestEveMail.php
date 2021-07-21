<?php


namespace Seatplus\Notifications\Tests\Stubs;


use Seatplus\Notifications\Notifications\NewEveMail;

class TestEveMail extends NewEveMail
{

    public static function getIcon(): string
    {
        return 'icon';
    }

    public static function getTitle(): string
    {
        return 'icon';
    }

    public static function getDescription(): string
    {
        return 'icon';
    }

    public static function getPermission(): string
    {
        return 'icon';
    }

    public static function getCorporationRole(): string
    {
        return '';
    }

    public function via(mixed $notifiable): array
    {
        return ['test-channel'];
    }
}