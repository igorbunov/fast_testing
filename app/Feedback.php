<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public static function add(string $message, string $email): Feedback
    {
        $record = new Feedback();

        $record->message = $message;
        $record->email = $email;

        $record->save();

        return $record;
    }

    public static function setSended(int $id)
    {
        $record = self::find($id);

        if (is_null($record)) {
            throw new \Exception(__('messages.feedback not found'));
        }

        $record->is_sended = 1;

        $record->save();

        return $record;
    }
}
