<?php

namespace Tests\Support;

use Netro\Support\Mail;
use Tests\NetroTestCase;

/**
 * Class MailTest
 * @package Tests\Support
 */
class MailTest extends NetroTestCase
{
    public function testTo()
    {
        $mail = new Mail();
        $mail->to('lucas@loeffel.io', 'Lucas Löffel');
        $mail->to('random@loeffel.io');

        $toValue = $this->getSecretProperty($mail, 'to')->getValue($mail);

        $this->assertEquals($toValue, [
            'Lucas Löffel <lucas@loeffel.io>',
            'random@loeffel.io'
        ]);
    }

    public function testFrom()
    {
        $mail = new Mail();
        $mail->from('lucas@loeffel.io', 'Lucas Löffel');

        $fromValue = $this->getSecretProperty($mail, 'from')->getValue($mail);

        $this->assertEquals($fromValue, 'From: Lucas Löffel <lucas@loeffel.io>');
    }

    public function testSubject()
    {
        $mail = new Mail();
        $mail->subject('Subject test');

        $subjectValue = $this->getSecretProperty($mail, 'subject')->getValue($mail);

        $this->assertEquals($subjectValue, 'Subject test');
    }

    public function testMessage()
    {
        $mail = new Mail();
        $mail->message('Message test');

        $messageValue = $this->getSecretProperty($mail, 'message')->getValue($mail);

        $this->assertEquals($messageValue, 'Message test');
    }

    public function testHeader()
    {
        $mail = new Mail();
        $mail->header('Content-Type: text/html; charset=UTF-8');
        $mail->header('X-Mailer: PHP/' . phpversion());

        $headerValue = $this->getSecretProperty($mail, 'header')->getValue($mail);

        $this->assertEquals($headerValue, [
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: PHP/' . phpversion(),
        ]);
    }
}
