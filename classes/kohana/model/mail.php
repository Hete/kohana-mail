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
     * 
     * @param variant $receiver people who will receive this mail.
     * @param variant $content mail's content stored in a view.
     * @param string $subject mail's subject.
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
                $headers[$key] = trim("$key: " . $value);
            }

            return implode("\r\n", $headers) . "\r\n";
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
     * Set or get the receiver of this mail.
     * 
     * @return Mail_Receiver
     */
    public function receiver($receiver = NULL) {

        if ($receiver === NULL) {
            return $this->receiver;
        }

        $email = $receiver;

        if (is_string($email) && Valid::email($email)) {
            $receiver = Model::factory('Mail_Receiver');
            $receiver->email = $email;
        }

        $this->receiver = $receiver;

        return $this;
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
            return $this->subject;
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
