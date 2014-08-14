<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Driver for built-in mail() PHP function.
 *
 * @uses mail
 *      
 * @package Mail
 * @category Senders
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_Mail extends Mail_Sender {

	protected function _send(array $to)
	{
		$headers = $this->headers;

		$body = $this->body;

		$attachments = $this->attachments;

		$boundary = sha1(uniqid(NULL, TRUE));

		if ($this->attachments)
		{
			// the body is the first part of the message
			array_unshift($attachments, array('attachment' => $body,
				'headers' => array(
					'Content-Type' => Arr::get($headers, 'Content-Type', 'text/plain'))));

			$body = 'This is a message with multiple parts in MIME format.' . "\r\n";
			$body .= '--' . $boundary . "\r\n";

			// override Content-Type of the message as it is a multipart
			$headers['Content-Type'] = "multipart/mixed; boundary=$boundary";
		}

		foreach ($attachments as $index => $attachment)
		{
			$attachment['headers']['Content-Transfer-Encoding'] = 'base64';

			foreach ($attachment['headers'] as $key => $value)
			{
				$body .= "$key: " . mb_encode_mimeheader($value) . "\r\n";
			}

			$body .= "\r\n";

			$body .= base64_encode($attachment['attachment']) . "\r\n";

			$body .= '--' . $boundary . ($index + 1 === count($attachments) ? '--' : '') . "\r\n";
		}

		$subject = NULL;

		// avoid duplicated Subject header
		if (array_key_exists('Subject', $headers))
		{
			$subject = mb_encode_mimeheader($headers['Subject']);

			unset($headers['Subject']);
		}

		$encoded_headers = array();

		foreach ($headers as $key => $value)
		{
			$value = mb_encode_mimeheader($value);
			$encoded_headers[] = "$key: $value";
		}

		$to = implode(', ', $to);

		$headers = implode("\r\n", $encoded_headers);

		$options = implode(' ', $this->options);

		return mail($to, $subject, $body, $headers, $options);
	}

}
