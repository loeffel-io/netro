<?php

namespace Netro\Support;

/**
 * Class Mail
 * @package Netro\Support
 */
class Mail
{
    /** @var array $from */
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
}
