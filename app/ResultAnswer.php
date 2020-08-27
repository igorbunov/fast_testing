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

    public static function getByResultQuestionAnswer(int $resultId, int $questionId, int $answerId): bool
    {
        $res = self::where([
            self::RESULT_ID => $resultId,
            self::QUESTION_ID => $questionId,
            self::ANSWER_ID => $answerId
        ])->first();
//        dd($res);
        if (is_null($res)) {
            return false;
        }

        return $res->is_checked > 0;
    }

    public static function getByResultId(int $resultId)
    {
//        SELECT * FROM result_answers WHERE result_id = 30
        $res = self::where([
            self::RESULT_ID => $resultId
        ])->get();

        if (!is_null($res)) {
            return $res->toArray();
        }

        return [];
    }
}
