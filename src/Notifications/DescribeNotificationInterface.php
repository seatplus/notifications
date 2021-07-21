<?php


namespace Seatplus\Notifications\Notifications;

interface DescribeNotificationInterface
{
    /**
     * Should return the heroicon name
     * https://unpkg.com/browse/@heroicons/vue@1.0.2/outline/
     * @return string
     */
    public static function getIcon(): string;

    public static function getTitle(): string;

    public static function getDescription(): string;

    public static function getPermission(): string;

    public static function getCorporationRole(): string;
}
