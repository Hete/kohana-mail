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

        $body = $this->body;

        if ($this->attachments) {

            $boundary = sha1(uniqid(NULL, TRUE));

            // The body is base64 encoded since it could break the multipart.
            $body = implode("\r\n", array(
                '--' . $boundary,
                'Content-Type: ' . Arr::get($this->headers, 'Content-Type', 'text/plain'),
                'Content-Transfer-Encoding: base64',
                "\r\n",
                base64_encode($this->body)
            ));

            $headers['Content-Type'] = 'multipart/mixed; boundary=' . $boundary;
        }

        foreach ($this->attachments as $index => $attachment) {

            $attachment_headers = array();

            foreach ($attachment['headers'] as $key => $value) {
                $attachment_headers[] = "$key: " . mb_encode_mimeheader($value);
            }

            $body .= implode("\r\n", array(
                '--' . $boundary . ($index + 1 === count($attachment) ? '--' : ''),
                implode("\r\n", $attachment_headers),
                'Content-Transfert-Encoding: base64',
                "\r\n",
                base64_encode($attachment['attachment'])
            ));
        }

        $headers = array();

        foreach ($this->headers as $key => $value) {
            $value = mb_encode_mimeheader($value);
            $headers[$key] = "$key: $value";
        }

        $headers = implode("\r\n", $headers);

        $subject = Arr::get($headers, 'Subject');

        $options = implode(' ', $this->options);

        return mail($to, $subject, $body, $headers, $options);
    }

}
