<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Result extends Model
{
    public $timestamps = false;

    const TEST_ID = 'test_id';
    const EMAIL = 'email';
    const STATUS = 'status';
    const START_DT = 'start_dt';
    const END_DT = 'end_dt';

    const STATUS_STARTED = 'started';
    const STATUS_FINISHED = 'finished';
    const STATUS_TIMEOUT = 'timeout';

    const DT_NOW = 'now()';

    public static function getSuccessResult(int $resultId): array
    {
        $res = DB::select("SELECT
                a.question_id,
                IF (a.right > 0 AND a.wrong = 0, 1, 0) AS is_correct
            FROM (
            SELECT 
                r.question_id,
                SUM(IF (r.is_checked = 1 AND a.is_true = 1, 1, 0)) AS `right`,
                SUM(IF (r.is_checked = 1 AND a.is_true = 0, 1, 0)) AS `wrong` 
            FROM result_answers r
            INNER JOIN answers a ON r.answer_id = a.id AND r.question_id = a.question_id
            WHERE r.result_id = {$resultId}
            GROUP BY r.question_id
            ) a");

        if (is_null($res)) {
            return [
                'total' => 0,
                'correct' => 0
            ];
        }

        $totalQuestions = count($res);
        $rightAnswers = 0;

        foreach ($res as $row) {
            if ($row->is_correct > 0) {
                $rightAnswers++;
            }
        }

        return [
            'total' => $totalQuestions,
            'correct' => $rightAnswers
        ];
    }

    public static function getByTestId(int $testId)
    {
        $res = DB::select("SELECT 
                id,
                test_id,
                email,
                `status`,
                 DATE_FORMAT(start_dt, '%d.%m.%Y %H:%i') AS start_dt,
                 DATE_FORMAT( end_dt, '%d.%m.%Y %H:%i') AS end_dt,
                 '' as report
            FROM results 
            WHERE test_id = {$testId} 
              AND `status` = '" . self::STATUS_FINISHED . "'");

        if (is_null($res)) {
            return array();
        }

        return $res;
    }

    public static function getSecondsToEnd(int $testId, int $resultId)
    {
        return DB::select("SELECT 
            TIME_TO_SEC(TIMEDIFF((r.start_dt + INTERVAL t.test_time_minutes MINUTE), NOW())) AS seconds_left
        FROM tests t
        INNER JOIN results r ON r.test_id = t.id
        WHERE t.id = {$testId} AND r.id = {$resultId}")[0]->seconds_left;
    }

    public static function getById(int $id): Result
    {
        return self::find($id);
    }

    private static function validateStatus(string $status): bool
    {
        return in_array($status, [
            self::STATUS_STARTED,
            self::STATUS_FINISHED,
            self::STATUS_TIMEOUT
        ]);
    }

    public static function add(array $data): Result
    {
        $record = new Result();

        foreach ($data as $field => $value) {
            $record->$field = $value;
        }

        if (!array_key_exists(self::STATUS, $data)) {
            $record->status = self::STATUS_STARTED;
        } elseif (!self::validateStatus($record->status)) {
            throw new \Exception(__('messages.wrong status'));
        }

        $record->save();

        return $record;
    }

    public static function edit(int $id, array $data): Result
    {
        $record = self::find($id);

        if (is_null($record)) {
            throw new \Exception(__('messages.result not found'));
        }

        foreach ($data as $field => $value) {
            if ($field == self::END_DT and $value == self::DT_NOW) {
                $record->end_dt = self::getNow();
            } else {
                $record->$field = $value;
            }
        }

        if (!array_key_exists(self::STATUS, $data)) {
            $record->status = self::STATUS_STARTED;
        } elseif (!self::validateStatus($record->status)) {
            throw new \Exception(__('messages.wrong status'));
        }

        $record->save();

        return $record;
    }

    private static function getNow()
    {
        return DB::select("SELECT NOW() as now")[0]->now;
    }

    public static function getDelayedTests(int $testId, int $timeForTestInMinutes): array
    {
        $res = DB::select("SELECT * 
            FROM results 
            WHERE test_id = {$testId} 
            AND `status` = '" . self::STATUS_STARTED . "'
            AND NOW() > start_dt + INTERVAL {$timeForTestInMinutes} minute");

        if (is_null($res)) {
            return array();
        }

        return $res;
    }
}
