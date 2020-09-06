<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public static function add(string $message): Feedback
    {
        $record = new Feedback();

        $record->message = $message;

        $record->save();

        return $record;
    }
}
