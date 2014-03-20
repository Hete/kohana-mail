<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender based on PEAR mail built-in function.
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency
 * @license  BSD 3-clauses
 */
class Kohana_Mail_Sender_PEAR_Mail extends Mail_Sender_PEAR {

    protected function PEAR_send(array $to, array $headers, $body) {
        return Mail::factory('mail', $this->options)->send($to, $headers, $body);
    }

}
