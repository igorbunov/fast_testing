<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultAnswer extends Model
{
    public $timestamps = false;

    const RESULT_ID = 'result_id';
    const QUESTION_ID = 'question_id';
    const ANSWER_ID = 'answer_id';
    const IS_CHECKED = 'is_checked';

    public static function add(array $data): ResultAnswer
    {
        $record = new ResultAnswer();

        foreach ($data as $field => $value) {
            $record->$field = $value;
        }

        $record->save();

        return $record;
    }
}
