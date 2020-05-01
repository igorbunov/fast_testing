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
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function newTest(): Test
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
            self::EDIT_SLUG => $editSlug
        ]);
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
}
