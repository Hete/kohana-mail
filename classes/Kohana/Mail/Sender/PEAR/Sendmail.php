<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PEAR_Sendmail extends Mail_Sender {

    protected function _send($email, $subject, $body, array $headers) {

        $headers['Subject'] = $subject;

        return Mail::factory('sendmail', Kohana::$config->load('mail.sender.PEAR.Sendmail'))
            ->send($email, $headers, $body);
    }
}
