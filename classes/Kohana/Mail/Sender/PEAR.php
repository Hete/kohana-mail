<?php

defined('SYSPATH') or die('No direct script access.');

require_once 'Mail.php';
require_once 'Mail/mime.php';

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

    protected function _send(array $to) {

        $mime = new Mail_MIME();

        if ($this->headers('Content-Type') === 'text/html') {

            $mime->setHTMLBody($this->body);
        } else {

            $mime->setTxtBody($this->body);
        }

        foreach ($this->attachments as $attachment) {

            $mime->addAttachment($attachment['attachment'], $attachment['headers'], FALSE);
        }        
 
        // PEAR use some old code that causes exceptions on E_STRICT...

        $saved = error_reporting();

        error_reporting($saved & ~E_STRICT);
        
        $status = $this->PEAR_send($to, $mime->headers($this->headers), $mime->get());

        error_reporting($saved);

        return $status;
    }

    /**
     * Abstracts the sending process for PEAR based senders.
     * 
     * @param mixed     $email
     * @param Mail_MIME $body
     * @param array     $headers
     */
    protected abstract function PEAR_send(array $to, array $headers, $body);
}
