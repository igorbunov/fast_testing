<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Test extends Model
{
    const TEST_SLUG = 'test_slug';
    const EDIT_SLUG = 'edit_slug';
    const DESCRIPTION = 'description';
    const TEST_TIME_MINUTES = 'test_time_minutes';
    const IS_ACTIVE = 'is_active';
    const EMAIL = 'email';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function newTest($email, $description, $testTime): Test
    {
        $editSlug = '';
        $testSlug = '';

        do {
            $editSlug = md5(Str::uuid()->toString());
        } while (!self::isUniqueSlug(self::EDIT_SLUG, $editSlug));

        do {
            $testSlug = substr(md5(Str::uuid()->toString()), 0, 8);
        } while (!self::isUniqueSlug(self::TEST_SLUG, $testSlug));

        return self::add([
            self::TEST_SLUG => $testSlug,
            self::EDIT_SLUG => $editSlug,
            self::DESCRIPTION => $description,
            self::IS_ACTIVE => 1,
            self::TEST_TIME_MINUTES => $testTime,
            self::EMAIL => $email
        ]);
    }

    public static function getByTestSlug(string $testSlug): Test
    {
        return self::where([
            self::TEST_SLUG => $testSlug
        ])->first();
    }

    public static function getByEditSlug(string $editSlug): Test
    {
        return self::where([
            self::EDIT_SLUG => $editSlug
        ])->first();
    }

    private static function isUniqueSlug(string $fieldName, string $slug): bool
    {
        $res = self::where([
            $fieldName => $slug
        ])->get();

        return (count($res) == 0);
    }

    public static function edit(int $id, array $data): Test
    {
        $record = self::find($id);

        if (is_null($record)) {
            throw new \Exception(__('messages.test not found'));
        }

        foreach ($data as $field => $value) {
            if (self::IS_ACTIVE == $field) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            $record->$field = $value;
        }

        $record->save();

        return $record;
    }

    private static function add(array $data): Test
    {
        $record = new Test();

        foreach ($data as $field => $value) {
            $record->$field = $value;
        }

        if (!array_key_exists(self::DESCRIPTION, $data)) {
            $record->description = '';
        }

        if (!array_key_exists(self::TEST_TIME_MINUTES, $data)) {
            $record->test_time_minutes = 30;
        }

        if (!array_key_exists(self::IS_ACTIVE, $data)) {
            $record->is_active = 0;
        }

        $record->save();

        return $record;
    }

    public static function getActiveTests(): array
    {
        $res = self::where([
            self::IS_ACTIVE => 1
        ])->get();

        if (is_null($res)) {
            return array();
        }

        return $res->toArray();
    }
}
