<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PEAR_Mail extends Mail_Sender_PEAR {

    protected function PEAR_send($email, Mail_Mime $mime, array $headers) {
        return Mail::factory('mail', Kohana::$config->load('mail.sender.PEAR.Mail'))
            ->send($email, $mime->headers($headers), $mime->get());
    }
}
