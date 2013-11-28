<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * @package  Mail
 * @category Sender
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
class Kohana_Mail_Sender_PEAR_SMTP extends Mail_Sender {

    protected function _send(Model_Mail $mail) {
        return (bool) Mail::factory("smtp", $this->config())->send($mail->receiver->receiver_email(), $mail->headers, $mail->render());
    }

}
