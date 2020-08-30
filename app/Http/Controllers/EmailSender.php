<?php
namespace App\Http\Controllers;

class EmailSender extends Controller
{
    private $toEmail;
    private $subject;
    private $headers;
    private $fromEmail = 'igorbunov.ua@gmail.com';

    public function __construct($toEmail, $subject)
    {
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
                <p>Поздравляю. Вы успешно создали свой тест</p>
                <p>Для прохождения теста, дайте участникам эту ссылку ' . $testLink . '</p>
                <p>Для просмотра результатов тестирования перейдите сюда: ' . $resultsLink . '</p>
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
                <p>Кто-то прошел ваш тест, его email: ' . $email . '</p>
                <p>Для просмотра результатов тестирования перейдите сюда: ' . $resultsLink . '</p>
                </div>
            </body>
            </html>';

        return mail($this->toEmail, $this->subject, $message, $this->headers);
    }
}