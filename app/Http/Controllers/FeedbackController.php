<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function showForm()
    {
        return view('feedback', [
            'translateValues' => (new TranslateController())->getValues()
        ]);
    }

    public function addFeedback(Request $request)
    {
        $email = $request->post('email', '');
        $message = $request->post('message', '');
        $name = $request->post('name', '');
//dd($email, $message);
        if (!empty($name)) {
            return;
        }

        if (empty($email)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.email cant be empty')
            ]);
        }

        if (empty($message)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.no message')
            ]);
        }

        $feedback = Feedback::add($message, $email);

        if (is_null($feedback)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unknown error')
            ]);
        }

        $emailSender = new EmailSender(env('CREATOR_EMAIL'), 'Оставлен отзыв');
        $isEmailSended = $emailSender->sendFeedback($email, $message);

        if (!$isEmailSended) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error sending message to email')
            ]);
        }

        Feedback::setSended($feedback->id);

        return response()->json([
            'success' => true,
            'message' => __('messages.feedback sended')
        ]);
    }
}
