<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Driver for sendmail built-in php function.
 * 
 * @package   Mail
 * @category  Senders
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_Mail extends Mail_Sender {

    protected function _send($email, $subject, $body, array $headers) {  

        if(Arr::is_array($email)) {
            $email = implode(', ', $email);
        }

        $subject = mb_encode_mimeheader($subject, mb_internal_encoding(), 'B', '');

        foreach($headers as $key => $value) {
            $value = mb_encode_mimeheader($value, mb_internal_encoding(), 'B', '');
            $headers[$key] = "$key: $value";
        }
   
        $headers = implode("\r\n", $headers);

        $parameters = implode(' ', Kohana::$config->load('mail.sender.Mail'));

        return mail($email, $subject, $body, $headers, $parameters);
    }

}
