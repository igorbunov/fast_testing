<?php

namespace App\Http\Controllers;

use App\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function showForm()
    {
        return view('feedback');
    }

    public function addFeedback(Request $request)
    {
        $message = $request->post('message');

        if (!empty($message)) {
            Feedback::add($message);

            return redirect('/');
        }

        return redirect('feedback');
    }
}
