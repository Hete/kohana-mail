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
class Kohana_Model_Mail extends Model {

    /**
     * List of formats to callback.
     * 
     * @var array 
     */
    public static $formats = array(
        'B' => 'base64_encode',
    );

    /**
     *
     * @var type 
     */
    public static $headers_eol = "\r\n";

    /**
     *
     * @var Mail_Receiver 
     */
    private $receiver;

    /**
     *
     * @var array 
     */
    private $headers = array();

    /**
     *
     * @var string 
     */
    private $subject;

    /**
     *
     * @var string 
     */
    private $body;

    /**
     *
     * @var array 
     */
    private $attachements = array();

    /**
     * Encode a value for headers. Has specific consideration for email 
     * addresses in the "name <email>" format.
     * 
     * @param string $value a string to encode.
     * @param string $encoding is $value encoding. Defaulted to utf-8.
     * @return string an encoded version of this string.
     */
    public static function headers_encode($value, $encoding = 'UTF-8', $format = 'B') {

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
            return Model_Mail::headers_encode($name) . ' ' . $email;
        }

        // Call the right encoding function over the value
        $encoded_value = call_user_func(Model_Mail::$formats[$format], $value);

        return "=?$encoding?$format?$encoded_value?=";
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

        $name = $this->receiver->receiver_name();
        $email = $this->receiver->receiver_email();

        if ($name === NULL) {
            return $email;
        }

        return Model_Mail::headers_encode($name) . " <$email>";
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
    public function body($body = NULL) {

        if ($body === NULL) {
            return $this->body;
        }

        $this->body = (string) $body;

        return $this;
    }

    /**
     * Adds or get attachements to this mail.
     * 
     * Attachements can be a single file content or multiple files contents
     * stored in an array.
     * 
     * @param  variant $attachements
     * @return array
     */
    public function attachements($attachement = NULL) {

        if ($attachement === NULL) {
            return $this->attachements;
        }

        if (Arr::is_array($attachement)) {
            $this->attachements = $attachement;
        } else {
            $this->attachements[] = (string) $attachement;
        }
        
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
            return $this->headers;
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

    /**
     * Get encoded headers for this mail.
     * 
     * @return array
     */
    public function headers_encoded() {
        // Renderig headers

        $headers = array();

        foreach ($this->headers as $key => $value) {
            $headers[] = trim("$key: " . Model_Mail::headers_encode($value));
        }

        return implode(Model_Mail::$headers_eol, $headers);
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
        return $this->body;
    }

    public function __toString() {
        return $this->render();
    }

}

?>
