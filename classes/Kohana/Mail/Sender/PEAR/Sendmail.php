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

    protected function PEAR_send(array $to, array $headers, $body) {

        $mail = new Mail();
        
        return $mail->factory('sendmail', $this->options)->send($to, $headers, $body);
    }

}
