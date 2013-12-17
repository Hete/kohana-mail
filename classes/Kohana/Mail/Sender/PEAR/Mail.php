<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PEAR_Mail extends Mail_Sender_PEAR {

    protected function _send($email, $subject, $body, array $headers) {

        $headers['Subject'] = $subject;

        return Mail::factory('mail', Kohana::$config->load('mail.sender.PEAR.Mail'))
            ->send($email, $headers, $body);
    }
}
