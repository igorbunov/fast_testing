<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    const TEST_ID = 'test_id';
    const QUESTION = 'question';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function isLinkedToSlug(int $questionId, string $slug): bool
    {
        $record = self::find($questionId);

        if (is_null($record)) {
            return false;
        }

        $test = Test::getByEditSlug($slug);

        if (is_null($test)) {
            return false;
        }

        return $test->id == $record->test_id;
    }

    public static function deleteById(int $questionId)
    {
        $record = self::find($questionId);

        if (is_null($record)) {
            throw new \Exception(__('messages.test not found'));
        }

        $record->destroy($questionId);
    }

    public static function newQuestion(int $testId, string $question = ''): Question
    {
        return self::add([
            self::TEST_ID => $testId,
            self::QUESTION => $question
        ]);
    }

    public static function getQuestionsByTestId(int $testId): array
    {
        $res = self::where([
            self::TEST_ID => $testId
        ])->get();

        if (!is_null($res)) {
            return $res->toArray();
        }

        return [];
    }

    public static function edit(int $id, string $questionText): Question
    {
        $record = self::find($id);

        if (is_null($record)) {
            throw new \Exception(__('messages.test not found'));
        }

        $record->question = $questionText;
        $record->save();

        return $record;
    }

    private static function add(array $data): Question
    {
        $record = new Question();

        foreach ($data as $field => $value) {
            $record->$field = $value;
        }

        if (!array_key_exists(self::QUESTION, $data)) {
            $record->question = '';
        }

        $record->save();

        return $record;
    }
}
