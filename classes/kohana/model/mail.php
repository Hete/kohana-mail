<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Model for mail.
 * 
 * @see Model_Validation
 * 
 * @package Mail
 * @category Model
 * @author Guillaume Poirier-Morency
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Model_Mail extends Model_Validation {

    /**
     * 
     * @param Mail_Receiver $receiver people who will receive this mail.
     * @param View $content mail's content stored in a view.
     * @param type $subject mail's subject.
     * @param array $headers headers
     */
    public function __construct(Mail_Receiver $receiver, $subject, View $content, array $headers = array()) {

        parent::__construct();

        $this->headers($headers)
                ->receiver($receiver)
                ->subject($subject)
                ->content($content);
    }

    /**
     * 
     * @param string $key
     * @param variant $value
     * @return Model_Mail|string in get mode, returns the headers value for key
     * $key, otherwise returns this object for builder syntax.
     */
    public function headers($key = NULL, $value = NULL) {

        if ($key === NULL) {

            $output = array();

            foreach ($this->headers as $key => $value) {
                $output[] = "$key: $value";
            }

            return implode("\r\n", $output);
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
     * 
     * @return Mail_Receiver
     */
    public function receiver(Mail_Receiver $receiver = NULL) {

        if ($receiver === NULL) {
            return $this->receiver;
        }

        $this->receiver = $receiver;

        // Update receiver in headers
        $this->headers("To", $receiver->receiver_name() . " <" . $receiver->receiver_email() . ">");

        return $this;
    }

    public function subject($subject = NULL) {

        if ($subject === NULL) {
            return '=?UTF-8?B?' . base64_encode($this->subject) . '?=';
        }

        $this->subject = $subject;

        $this->headers("Subject", $subject);

        return $this;
    }

    public function content(View $content = NULL) {

        if ($content === NULL) {
            return $this->content;
        }

        $this->content = $content;

        return $this;
    }

    public function render() {
        return $this->content->render();
    }

    public function __toString() {
        return $this->render();
    }

}

?>
