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
     * List of encoding format to callback used to encode them.
     * 
     * @var array 
     */
    public static $output_format_callbacks = array(
        'B' => 'base64_encode',
    );

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
     * Encode a value for headers. Has specific consideration for email 
     * addresses in the "name <email>" format.
     * 
     * @param string $value a string to encode.
     * @param string $encoding is $value encoding. Defaulted to utf-8.
     * @return string an encoded version of this string.
     */
    public static function headers_encode($value, $encoding = 'UTF-8', $output_format = 'B') {

        // Special encoding for lists
        if (preg_match('/,/', $value)) {

            $parts = explode(',', $value);

            foreach ($parts as &$part) {
                $part = static::headers_encode($part);
            }

            return implode(',', $parts);
        }

        // Do not convert ascii strings
        if (!preg_match('/[^\x00-\x7F]/', $value)) {
            return $value;
        }

        $email_regex = '\w+@\w+\.\w+';

        // Special encoding for name <email>
        if (preg_match("/[\w\s]+<$email_regex>/", $value)) {

            // Match only email including <>
            $matches = array();

            preg_match("/<$email_regex>/", $value, $matches);

            $email = $matches[0];

            $name = trim(preg_replace("/<$email_regex>/", '', $value));

            // Reencode name, email must be ascii-compliant
            return static::headers_encode($name) . ' ' . $email;
        }

        // Call the right encoding function over the value
        $encoded_value = call_user_func(static::$output_format_callbacks[$output_format], $value);

        return "=?$encoding?$output_format?$encoded_value?=";
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

        // Encode name if available
        if (Valid::not_empty($this->receiver->receiver_name())) {
            $this->headers_encode($this->receiver->receiver_name()) . ' <' . $this->receiver()->receiver_email() . '>';
        }

        return $this->receiver->receiver_email();
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

            $headers = array();

            foreach ($this->headers as $key => $value) {
                $headers[] = trim("$key: " . static::headers_encode($value));
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

    public function from($from = NULL) {
        return $this->headers('From', $from);
    }

    public function cc($cc = NULL) {
        return $this->headers('Cc', $cc);
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
