<?php

defined('SYSPATH') or die('No direct script access.');

require_once 'Mail.php';
require_once 'Mail/mime.php';

/**
 * PEAR wrapper for the Mail module.
 *
 * PEAR must be included in your PHP path.
 *
 * @uses Mail
 * @uses Mail_mime
 *      
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license BSD-3-Clauses
 */
abstract class Kohana_Mail_Sender_PEAR extends Mail_Sender {

	protected function _send(array $to)
	{
		$mime = new Mail_mime();

		if ($this->headers('Content-Type') === 'text/html')
		{
			$mime->setHTMLBody($this->body);
		}
		else
		{
			$mime->setTxtBody($this->body);
		}

		foreach ($this->attachments as $attachment)
		{
			$headers = $attachment['headers'];

			$disposition = Arr::get($headers, 'Content-Disposition', 'attachment');
			$filename = NULL;

			if (strpos($disposition, ';filename=') !== FALSE)
			{
				list ($disposition, $filename) = preg_split('/;filename=/', $disposition);
			}

			$mime->addAttachment($attachment['attachment'], Arr::get($headers, 'Content-Type', 'application/octect-stream'), $filename, FALSE, 'base64', $disposition);
		}

		return $this->PEAR_send($to, $mime->headers($this->headers), $mime->get());
	}

	/**
	 * Abstracts the sending process for PEAR based senders.
	 *
	 * @param array $to        	
	 * @param array $headers        	
	 * @param string $body        	
	 */
	protected abstract function PEAR_send(array $to, array $headers, $body);
}
