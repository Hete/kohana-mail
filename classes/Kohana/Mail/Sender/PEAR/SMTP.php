<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Error reporting is disabled for E_STRICT.
 */
error_reporting(error_reporting() & ~ E_STRICT);

/**
 * Sender based on PEAR SMTP.
 * 
 * @uses Mail_smtp
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD 3-clauses
 */
class Kohana_Mail_Sender_PEAR_SMTP extends Mail_Sender_PEAR {

    protected function PEAR_send(array $to, array $headers, $body) {

        $mail = new Mail();

        return $mail->factory('smtp', $this->options)->send($to, $headers, $body);
    }

}
