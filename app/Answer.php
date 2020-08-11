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

    public static function isLinkedToQuestion(int $answerId, int $questionId): bool
    {
        $res = self::where([
            'id' => $answerId,
            self::QUESTION_ID => $questionId
        ])->get();

        return !is_null($res);
    }

    public static function deleteById(int $answerId)
    {
        $record = self::find($answerId);

        if (is_null($record)) {
            throw new \Exception('Не найден ответ');
        }

        $record->delete();
    }

    public static function deleteByQuestionId(int $questionId)
    {
        $res = self::where([
            self::QUESTION_ID => $questionId
        ])->get();

        if (is_null($res)) {
            return;
        }

        foreach ($res as $row) {
            $row->destroy($row->id);
        }
    }

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

    public static function getQuestionId(int $id): int
    {
        $record = self::find($id);

        if (is_null($record)) {
//            dd($id, $record);
            throw new \Exception('Не найден ответ');
        }

        return $record->question_id;
    }

    public static function edit(int $id, string $answerText, bool $isTrue): Answer
    {
        $record = self::find($id);

        if (is_null($record)) {
            throw new \Exception('Не найден ответ');
        }

        $record->answer = $answerText;
        $record->is_true = ($isTrue) ? 1 : 0;
        $record->save();

        return $record;
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
