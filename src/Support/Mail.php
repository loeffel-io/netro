<?php

namespace Netro\Support;

/**
 * Class Mail
 * @package Netro\Support
 */
class Mail
{
    /** @var string $from */
    protected $from;

    /** @var array $to */
    protected $to;

    /** @var string $subject */
    protected $subject;

    /** @var string $message */
    protected $message;

    /** @var array $headers */
    protected $headers;

    /**
     * @param string $mail
     * @param string|null $name
     * @return Mail
     */
    public function to(string $mail, string $name = null): Mail
    {
        $this->to[] = ['mail' => $mail, 'name' => $name];

        return $this;
    }

    /**
     * @param string $mail
     * @param string $name
     * @return Mail
     */
    public function from(string $mail, string $name): Mail
    {
        $this->from = "$name <$mail>";

        return $this;
    }

    /**
     * @param string $subject
     * @return Mail
     */
    public function subject(string $subject): Mail
    {
        $this->subject = $subject;

        return $this;
    }
}
