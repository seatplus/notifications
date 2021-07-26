<?php

namespace Seatplus\Notifications\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Seatplus\Notifications\Notifications\DescribeNotificationInterface;

class Outbox extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getNotificationAttribute($value) : DescribeNotificationInterface
    {
        return unserialize($value);
    }

    public function setNotificationAttribute($value) : void
    {
        $this->attributes['notification'] = serialize($value);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }
}
