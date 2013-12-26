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

    protected function _send($email, $body, array $headers, array $attachments) {

        $mime = new Mail_mime();

        if($headers['Content-Type'] === 'text/html') {
            $mime->setHTMLBody($body);
        } else {
            $mime->setTxtBody($body);
        }

        foreach($attachments as $attachment) {

            list($attachment, $type) = $attachment;

            $mime->addAttachment($attachment, $type, FALSE);
        }

        return $this->PEAR_send($email, $mime, $headers);

    }

    protected abstract function PEAR_send($email, Mail_MIME $body, array $headers);
}
