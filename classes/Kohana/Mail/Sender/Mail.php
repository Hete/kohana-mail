<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Driver for built-in mail() PHP function.
 * 
 * @see mail
 *
 * @package   Mail
 * @category  Senders
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 */
class Kohana_Mail_Sender_Mail extends Mail_Sender {

    protected function _send(array $to) {

        $to = implode(', ', $to);

        $subject = $this->subject();

        $body = $this->body;

        $headers = array();

        foreach ($this->headers as $key => $value) {

            $value = mb_encode_mimeheader($value);
            $headers[] = "$key: $value";
        }

        $boundary = sha1(uniqid(NULL, TRUE));

        if ($this->attachments) {

            // the body is an attachment
            array_unshift($this->attachments, array(
                'headers' => array(
                    'Content-Type' => Arr::get($this->headers, 'Content-Type', 'text/plain')
                ),
                'attachment' => $this->body
            ));

            $headers[] = "Content-Type: multipart/mixed; boundary=$boundary";

            $body = 'This is a message with multiple parts in MIME format.' . "\r\n";
            $body .= '--' . $boundary . "\r\n";
        }

        foreach ($this->attachments as $index => $attachment) {

            $attachment_headers = array();

            foreach ($attachment['headers'] as $key => $value) {
                $attachment_headers[] = "$key: " . mb_encode_mimeheader($value);
            }

            $attachment_headers[] = 'Content-Transfer-Encoding: base64';

            $body .= implode("\r\n", $attachment_headers);

            $body .= "\r\n\r\n";

            $body .= base64_encode($attachment['attachment']) . "\r\n";

            $body .= '--' . $boundary . ($index + 1 === count($attachment) ? '--' : '') . "\r\n";
        }

        $subject = mb_encode_mimeheader($subject);

        $headers = implode("\r\n", $headers);

        $options = implode(' ', $this->options);

        return mail($to, $subject, $body, $headers, $options);
    }

}
