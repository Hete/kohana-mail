<?php

defined('SYSPATH') or die('No direct script access.');

require_once 'Mail.php';
require_once 'Mail/Mime.php';

/**
 * PEAR wrapper for the Mail module.
 *
 * PEAR must be included in your PHP path.
 *
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 */
abstract class Kohana_Mail_Sender_PEAR extends Mail_Sender {

    protected function _send(array $to, $body, array $headers, array $attachments) {

        $mime = new Mail_MIME();

        if ($headers['Content-Type'] === 'text/html') {
            $mime->setHTMLBody($body);
        } else {
            $mime->setTxtBody($body);
        }

        foreach ($attachments as $attachment) {
            list($attachment, $headers) = $attachment;
            $mime->addAttachment($attachment, $headers, FALSE);
        }

        return $this->PEAR_send($to, $mime, $headers);
    }

    /**
     * Abstracts the sending process for PEAR based senders.
     * 
     * @param mixed     $email
     * @param Mail_MIME $body
     * @param array     $headers
     */
    protected abstract function PEAR_send(array $to, Mail_MIME $body, array $headers);
}
