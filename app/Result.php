<?php

namespace App;

use Carbon\Carbon;
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
            throw new \Exception('wrong status');
        }

        $record->save();

        return $record;
    }

    public static function edit(int $id, array $data): Result
    {
        $record = self::find($id);

        if (is_null($record)) {
            throw new \Exception('Не найден результат');
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
            throw new \Exception('wrong status');
        }

        $record->save();

        return $record;
    }

    private static function getNow()
    {
        return DB::select("SELECT NOW() as now")[0]->now;
    }
}
