<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Driver for built-in mail() PHP function.
 *
 * @uses mail
 * 
 * @todo finish reimplementation with header encoding.
 *      
 * @package Mail
 * @category Senders
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_Mail extends Mail_Sender {

	/**
	 * Encode a mime header.
	 * 
	 * @param  string $header   header value
	 * @reutrn string
	 */
	public static function header_encode($name, $header)
	{
		// restrict encoding to specific headers
		if (! in_array($name, array('Subject', 'To', 'Reply-To', 'From', 'Cc', 'Bcc')))
		{
			return $header;
		}

		if (Arr::is_array($header))
		{
			$recipients = array();

			foreach ($header as $key => $value)
			{
				if (is_string($key) AND Valid::email($key))
				{
					// $key is an email, so $value is a name
					$recipients[] = static::header_encode($name, $value).' <'.$key.'>';
				}
				else
				{
					// $key is a numeric index, $value is an email
					$recipients[] = static::header_encode($name, $value);
				}
			}

			return join(', ', $recipients);
		}

		if (function_exists('mb_encode_mimeheader'))
		{
			return mb_encode_mimeheader($header);
		}

		// strip non-ascii
		return UTF8::strip_non_ascii($header);
	}

	public function error() 
	{
		return NULL;	
	}

	protected function _send()
	{
		$headers = $this->headers;

		$body = $this->body;

		$attachments = $this->attachments;

		$boundary = sha1(uniqid(NULL, TRUE));

		if ($this->attachments)
		{
			// the body is the first part of the message
			array_unshift($attachments, array(
				'attachment' => $body,
				'headers' => array(
					'Content-Type' => Arr::get($headers, 'Content-Type', 'text/plain')
				)
			));

			$body = 'This is a message with multiple parts in MIME format.'."\r\n";
			$body .= '--'.$boundary."\r\n";

			// override Content-Type of the message as it is a multipart
			$headers['Content-Type'] = "multipart/mixed; boundary=$boundary";
		}

		foreach ($attachments as $index => $attachment)
		{
			$attachment['headers']['Content-Transfer-Encoding'] = 'base64';

			foreach ($attachment['headers'] as $name => $header)
			{
				$body .= $name.': '.static::header_encode($name, $header)."\r\n";
			}

			$body .= "\r\n";

			$body .= base64_encode($attachment['attachment'])."\r\n";

			$body .= '--'.$boundary.($index + 1 === count($attachments) ? '--' : '')."\r\n";
		}

		$subject = NULL;

		// avoid duplicated Subject header
		if (array_key_exists('Subject', $headers))
		{
			$subject = $headers['Subject'];

			unset($headers['Subject']);
		}

		$encoded_headers = array();

		foreach ($headers as $name => $header)
		{
			$encoded_headers[] = $name.': '.static::header_encode($name, $header);
		}

		$to = static::header_encode('To', $this->to);

		$headers = implode("\r\n", $encoded_headers);

		$options = implode(' ', $this->options);

		return mail($to, $subject, $body, $headers, $options) AND ! empty($body);
	}

}
