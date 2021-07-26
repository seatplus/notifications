<?php


namespace Seatplus\Notifications\Observers;


use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Notifications\Jobs\NewEveMail;

class EveMailObserver
{

    public function created(Mail $mail)
    {
        dispatch(new NewEveMail($mail))->onQueue('medium')->delay(now()->addSeconds(30));
    }

}