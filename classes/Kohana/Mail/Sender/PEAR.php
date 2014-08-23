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
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD-3-Clauses
 */
abstract class Kohana_Mail_Sender_PEAR extends Mail_Sender {

	/**
	 *
	 * @var Mail 
	 */
	protected $mail;

	public function __construct(array $options = NULL)
	{
		parent::__construct($options);

		$this->mail = new Mail();
	}

	protected function _send()
	{
		$mime = new Mail_mime();

		foreach ($this->headers as $name => $header)
		{
			$mime->addHeader($name, $header);
		}

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
			$mime->addAttachment($attachment['attachment'], $attachment['headers'], FALSE);
		}

		return $this->mail->send($this->to, $mime->headers(), $mime->get());
	}

}
