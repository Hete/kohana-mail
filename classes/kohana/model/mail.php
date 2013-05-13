<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Model for mail. Use a sender to send it.
 * 
 * @package Mail
 * @category Model
 * @author Guillaume Poirier-Morency
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Model_Mail extends Model_Validation {

    /**
     *
     * @var Mail_Receiver 
     */
    private $receiver;

    /**
     *
     * @var array 
     */
    private $headers;

    /**
     *
     * @var string 
     */
    private $subject;

    /**
     *
     * @var string 
     */
    private $content;

    /**
     * Encode a value for headers.
     * 
     * @param string $value a string to encode.
     * @param string $encoding is $value encoding. Defaulted to utf-8.
     * @return string an encoded version of this string.
     */
    public static function headers_encode($value, $encoding = 'utf-8') {
        return "=?$encoding?B?" . base64_encode((string) $value) . '?=';
    }

    /**
     * 
     * @param Mail_Receiver $receiver people who will receive this mail.
     * @param string $subject mail's subject.
     * @param variant $content mail's content stored in a view.
     * @param array $headers headers
     */
    public function __construct($receiver, $subject, $content, array $headers = array()) {

        // Update internals
        $this->headers($headers)
                ->subject($subject)
                ->receiver($receiver)
                ->content($content);
    }

    /**
     * Set or get the receiver of this mail.
     * 
     * @param Mail_Receiver $receiver
     * @return Mail_Receiver
     */
    public function receiver(Mail_Receiver $receiver = NULL) {

        if ($receiver === NULL) {
            return $this->receiver;
        }

        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Getter for to.
     * 
     * @return string
     */
    public function to() {
        return static::headers_encode($this->receiver()->receiver_name()) . '<' . static::headers_encode($this->receiver()->receiver_name()) . '>';
    }

    /**
     * Get or set the subject of this mail.
     * 
     * @param View $content
     * @return Model_Mail
     */
    public function subject($subject = NULL) {

        // Getter
        if ($subject === NULL) {
            return static::headers_encode($this->subject);
        }

        // Update subject
        $this->subject = (string) $subject;

        return $this;
    }

    /**
     * Get or set the content of this mail.
     * 
     * @param variant $content
     * @return Model_Mail
     */
    public function content($content = NULL) {

        if ($content === NULL) {
            return $this->content;
        }

        $this->content = (string) $content;

        return $this;
    }

    /**
     * Getter-setter for mail headers.
     * 
     * @param string $key
     * @param variant $value
     * @return Model_Mail|string in get mode, returns the headers value for key
     * $key, otherwise returns this object for builder syntax.
     */
    public function headers($key = NULL, $value = NULL) {

        if ($key === NULL) {

            foreach ($this->headers as $key => $value) {
                $headers[$key] = trim("$key: " . static::headers_encode($value));
            }

            return implode("\r\n", $headers);
        }

        if (Arr::is_array($key)) {
            $this->headers = $key;
            return $this;
        }

        if ($value === NULL) {
            return Arr::get($this->headers, $key);
        }

        // Always cast to string
        $this->headers[$key] = (string) $value;

        return $this;
    }

    public function cc($bcc = NULL) {
        return $this->headers('Cc', $bcc);
    }

    public function bcc($bcc = NULL) {
        return $this->headers('Bcc', $bcc);
    }

    public function reply_to($reply_to = NULL) {
        return $this->headers('Reply-To', $reply_to);
    }

    /**
     * Renders the mail.
     * 
     * @return string
     */
    public function render() {
        return (string) $this->content;
    }

    public function __toString() {
        return $this->render();
    }

}

?>
