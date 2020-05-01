<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    const QUESTION_ID = 'question_id';
    const ANSWER = 'answer';
    const IS_TRUE = 'is_true';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function newAnswer(int $questionId, string $answer = '', bool $isTrue = false): Answer
    {
        return self::add([
            self::QUESTION_ID => $questionId,
            self::ANSWER => $answer,
            self::IS_TRUE => $isTrue
        ]);
    }

    public static function getAnswersByQuestionId(int $questionId): array
    {
        $res = self::where([
            self::QUESTION_ID => $questionId
        ])->get();

        if (!is_null($res)) {
            return $res->toArray();
        }

        return [];
    }

    private static function add(array $data): Answer
    {
        $record = new Answer();

        foreach ($data as $field => $value) {
            $record->$field = $value;
        }

        if (!array_key_exists(self::ANSWER, $data)) {
            $record->answer = '';
        }

        if (!array_key_exists(self::IS_TRUE, $data)) {
            $record->is_true = 0;
        }

        $record->save();

        return $record;
    }
}
