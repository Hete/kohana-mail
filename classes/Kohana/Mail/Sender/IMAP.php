<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Use integrated imap_mail function.
 * 
 * @package Mail
 * @category Senders
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_IMAP extends Mail_Sender {

    protected function _send($email, $subject, $body, array $headers) {

        $subject = mb_encode_mimeheader($subject, mb_internal_encoding(), 'B', '');

        foreach($headers as $key in $value) {
            $headers[$key] = mb_encode_mimeheader($value, mb_internal_encoding(), 'B', '');
        }

        $parameters = Kohana::$config->load('mail.sender.IMAP');

        return imap_mail($email, $subject, $body, implode("\r\n", $headers), $parameters);
    }
}
