<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PEAR_SMTP extends Mail_Sender_PEAR {

    protected function PEAR_send($email, Mail_Mime $mime, array $headers) {
        return Mail::factory('smtp', Kohana::$config->load('mail.sender.PEAR.SMTP'))
            ->send($email, $mime->headers($headers), $mime->get());
    }

}
