<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PEAR_SMTP extends Mail_Sender_PEAR {

    protected function _pear_send($email, $body, $headers) {
        return Mail::factory('smtp', Kohana::$config->load('mail.sender.PEAR.SMTP'))
            ->send($email, $headers, $body);
    }

}
