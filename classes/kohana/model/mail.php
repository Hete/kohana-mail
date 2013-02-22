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
     * @var Mail_Receiver 
     */
    public $receiver;

    /**
     *
     * @var View
     */
    public $content,
            /**
             * @var string
             */
            $subject,
            /**
             * @var array
             */
            $headers;

    /**
     * 
     * @param Mail_Receiver $receiver people who will receive this mail.
     * @param View $content mail's content stored in a view.
     * @param type $subject mail's subject.
     * @param array $headers headers
     */
    public function __construct(Mail_Receiver $receiver, $subject, View $content, array $headers = NULL) {

        parent::__construct();

        $this->receiver = $receiver;
        $this->subject = $subject;
        $this->content = $content;
        $this->headers = $headers;
    }

    /**
     * 
     * @return Mail_Receiver
     */
    public function receiver() {
        return $this->receiver;
    }

    public function headers($key = NULL, $value = NULL) {

        if ($key === NULL) {

            $output = array();
            foreach ($this->headers as $key => $value) {
                $output[] = "$key: $value";
            }
            return implode("\r\n", $output);

            return $output;
        }

        if ($value === NULL) {
            return Arr::get($this->headers, $key);
        }

        $this->headers[$key] = $value;

        return $this;
    }

    public function subject($value = NULL) {

        if ($value === NULL) {
            return $this->subject;
        }

        $this->subject = '=?UTF-8?B?' . base64_encode($this->subject) . '?=';

        return $this;
    }

    public function content($value = NULL) {

        if ($value === NULL) {
            return $this->content;
        }

        $this->content = $value;

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
