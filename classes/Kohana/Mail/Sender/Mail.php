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

    protected function _send($email, $subject, $body, array $headers, array $attachments) {  

        if(Arr::is_array($email)) {
            $email = implode(', ', $email);
        }

        $subject = mb_encode_mimeheader($subject);

        if ($attachments) {

            // The body is base64 encoded since it could break the multipart.
            $body = implode("\r\n", array(
                '--' . Security::token(),
                'Content-Type: ' . mb_encode_mimeheader($headers['Content-Type']),
                'Content-Transfer-Encoding: base64',
                "\r\n",
                base64_encode($body)
            ));

            $headers['Content-Type'] = 'multipart/mixed; boundary=' . Security::token();
        }

        foreach($attachments as $attachment) {
            $body .= implode("\r\n", array(
                '--' . Security::token() . (($index + 1 === count($attachment)) ? '--' : ''),
                'Content-Type: ' . mb_encode_mimeheader($attachment['type']),
                'Content-Transfert-Encoding: base64',
                "\r\n",
                base64_encode($attachment['attachment'])
            ));
        }

        foreach($headers as $key => $value) {
            $value = mb_encode_mimeheader($value);
            $headers[$key] = "$key: $value";
        }
   
        $headers = implode("\r\n", $headers);

        $parameters = implode(' ', Kohana::$config->load('mail.sender.Mail'));

        return mail($email, $subject, $body, $headers, $parameters);
    }

}
