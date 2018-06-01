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

    /** @var array $header */
    protected $header;

    /**
     * Set to
     * @param string $mail
     * @param string|null $name
     * @return Mail
     */
    public function to(string $mail, string $name = null): Mail
    {
        $this->to[] = empty($name) ? $mail : "$name <$mail>";

        return $this;
    }

    /**
     * Set from
     * @param string $mail
     * @param string $name
     * @return Mail
     */
    public function from(string $mail, string $name): Mail
    {
        $this->from = "From: $name <$mail>";

        return $this;
    }

    /**
     * Set subject
     * @param string $subject
     * @return Mail
     */
    public function subject(string $subject): Mail
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set message
     * @param string $message
     * @return Mail
     */
    public function message(string $message): Mail
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set header
     * @param string $value
     * @return Mail
     */
    public function header(string $value): Mail
    {
        $this->header[] = $value;

        return $this;
    }

    /**
     * Send mail
     * @return bool
     */
    public function send(): bool
    {
        // Add from to header
        if (!empty($this->from)) {
            $this->header[] = $this->from;
        }

        return wp_mail(
            implode(",", $this->to),
            $this->subject,
            $this->message,
            $this->header
        );
    }
}
