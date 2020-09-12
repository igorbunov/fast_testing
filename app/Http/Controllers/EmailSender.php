<?php
namespace App\Http\Controllers;

class EmailSender extends Controller
{
    private $toEmail;
    private $subject;
    private $headers;
    private $fromEmail;

    public function __construct($toEmail, $subject)
    {
        $this->fromEmail = env('CREATOR_EMAIL');
        $this->toEmail = $toEmail;
        $this->subject = $subject;

        $headers = "" .
            "Reply-To:" . $this->fromEmail . "\r\n" .
            "X-Mailer: PHP/" . phpversion();
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: ' . $this->fromEmail . "\r\n";

        $this->headers = $headers;
    }

    public function sendTestCreated(string $testLink, string $resultsLink): bool
    {
        $message = '<html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title></title>
            </head>
            <body>
                <div id="email-wrap">
                <p>' . __('view.congratulations you have successfully created a test') . '</p>
                <p>' . __('messages.to take the test give the participants this link') . ' ' . $testLink . '</p>
                <p>' . __('messages.to view test results go here') . ': ' . $resultsLink . '</p>
                </div>
            </body>
            </html>';

        return mail($this->toEmail, $this->subject, $message, $this->headers);
    }

    public function sendTestPassed(string $email, string $resultsLink): bool
    {
        $message = '<html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title></title>
            </head>
            <body>
                <div id="email-wrap">
                <p>' . __('messages.someone passed your test their email') . ': ' . $email . '</p>
                <p>' . __('messages.to view test results go here') . ': ' . $resultsLink . '</p>
                </div>
            </body>
            </html>';

        return mail($this->toEmail, $this->subject, $message, $this->headers);
    }

    public function sendFeedback(string $email, string $message): bool
    {
        $message = '<html>
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title></title>
            </head>
            <body>
                <div>
                <p>Email: ' . $email . '</p>
                <p>Отзыв: ' . $message . '</p>
                </div>
            </body>
            </html>';

        return mail($this->toEmail, $this->subject, $message, $this->headers);
    }
}