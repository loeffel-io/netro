<?php

namespace Tests\Support;

use Netro\Support\Mail;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;

/**
 * Class MailTest
 * @package Tests\Support
 */
class MailTest extends TestCase
{
    /**
     * @param Mail $mail
     * @param string $property
     * @return ReflectionProperty
     * @throws ReflectionException
     */
    private function getSecretProperty(Mail $mail, string $property): ReflectionProperty
    {
        $reflection = new ReflectionClass($mail);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property;
    }

    public function testTo()
    {
        $mail = new Mail();
        $mail->to('lucas@loeffel.io', 'Lucas Löffel');
        $mail->to('random@loeffel.io');

        $value = $this->getSecretProperty($mail, 'to')->getValue($mail);

        $this->assertEquals($value, [
            ['mail' => 'lucas@loeffel.io', 'name' => 'Lucas Löffel'],
            ['mail' => 'random@loeffel.io', 'name' => null]
        ]);
    }
}
