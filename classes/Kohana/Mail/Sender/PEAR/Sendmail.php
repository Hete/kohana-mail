<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender based on PEAR Sendmail.
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD 3-clauses
 */
class Kohana_Mail_Sender_PEAR_Sendmail extends Mail_Sender_PEAR {

    protected function PEAR_send(array $to, Mail_Mime $mime, array $headers) {
        return Mail::factory('sendmail', $this->options)
                        ->send($to, $mime->headers($headers), $mime->get());
    }

}
