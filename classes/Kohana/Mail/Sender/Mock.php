<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Fake sender for testing application without sending real mails.
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD 3 clauses
 */
class Kohana_Mail_Sender_Mock extends Mail_Sender {

    /**
     * Stack of sent mail.
     * 
     * Use array_pop in your tests to ensure specific mail have been sent.
     * 
     * @var array 
     */
    public static $history;

    protected function _send(array $to) {

        $this->to = $to;

        // push the mail on the stack
        Mail_Sender_Mock::$history[] = $this;

        return (bool) $to;
    }

}