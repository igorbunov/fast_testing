<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    private $vals = [];

    public function __construct()
    {
        $this->vals = [
            'time for testing' => __('view.time for testing'),
            'minutes' => __('view.minutes'),
            'calculating time for testing' => __('view.calculating time for testing'),
            'you must specify the text of the question' => __('messages.you must specify the text of the question'),
            'you must specify the response text' => __('messages.you must specify the response text'),
            'you must provide the correct answer' => __('messages.you must provide the correct answer'),
            'you must add at least one question' => __('messages.you must add at least one question'),
            'confirmation' => __('messages.confirmation'),
            'yes' => __('messages.yes'),
            'no' => __('messages.no'),
            'error' => __('messages.error'),
            'close' => __('messages.close'),
            'system message' => __('messages.system message'),
            'test description required' => __('messages.test description required'),
            'your email is empty or not valid' => __('messages.your email is empty or not valid'),
            'do you really want to deactivate the test' => __('messages.do you really want to deactivate the test'),
            'test deactivated' => __('messages.test deactivated'),
            'you must enter your email' => __('messages.you must enter your email'),
            'test completed' => __('messages.test completed')
        ];
    }

    public function getValues(): array
    {
        return $this->vals;
    }
}
