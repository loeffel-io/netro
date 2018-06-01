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
            ['mail' => 'lucas@loeffel.io', 'name' => 'Lucas Löffel'],
            ['mail' => 'random@loeffel.io', 'name' => null]
        ]);
    }

    public function testFrom()
    {
        $mail = new Mail();
        $mail->from('lucas@loeffel.io', 'Lucas Löffel');

        $fromValue = $this->getSecretProperty($mail, 'from')->getValue($mail);

        $this->assertEquals($fromValue, 'Lucas Löffel <lucas@loeffel.io>');
    }
}
