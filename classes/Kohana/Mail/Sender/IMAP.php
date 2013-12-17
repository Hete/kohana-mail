<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Use integrated imap_mail function.
 * 
 * @package   Mail
 * @category  Senders
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_IMAP extends Mail_Sender {

    protected function _send($email, $subject, $body, array $headers) {

        if(Arr::is_array($email)) {
            $email = implode(', ', $email);
        }

        $subject = mb_encode_mimeheader($subject, mb_internal_encoding(), 'B', '');

        $cc = Arr::get($headers, 'Cc');

        $bcc = Arr::get($headers, 'Bcc');

        foreach($headers as $key => $value) {
            $value = mb_encode_mimeheader($value, mb_internal_encoding(), 'B', '');
            $headers[$key] = "$key: $value";
        }
        
        $headers = implode("\n\r", $headers);

        $rpath = Kohana::$config->load('mail.sender.IMAP.rpath');

        return imap_mail($email, $subject, $body, $headers, $cc, $bcc, $rpath);
    }
}
