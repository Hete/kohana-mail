<?php

defined('SYSPATH') OR die('No direct script access.');

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

	/** 
     *
	 * @var PEAR_Error
	 */
	private $error;

	public function __construct(array $headers, array $options)
	{
		parent::__construct($headers, $options);

		$this->mail = new Mail;
	}

	public function error()
	{
		return ($this->error instanceof PEAR_Error) ? $this->error->getMessage() : NULL;
	}

	protected function _send()
	{
		$mime = new Mail_mime(array('head_charset' => Kohana::$charset, 'text_charset' => Kohana::$charset, 'html_charset' => Kohana::$charset));

		if ($this->content_type() === 'text/html')
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

			$content_type = Arr::get($headers, 'Content-Type', 'application/octet-stream');
			$disposition = Arr::get($headers, 'Content-Disposition', 'attachment');
			$filename = NULL;
			$description = Arr::get($headers, 'Content-Description');
			$charset = Kohana::$charset;
			$language = Arr::get($headers, 'Content-Language');
			$location = Arr::get($headers, 'Content-Location');

			if (strpos($content_type, '; charset=') !== FALSE)
			{
				list ($content_type, $charset) = preg_split('/; charset=/', $content_type);
			}

			if (strpos($disposition, '; filename=') !== FALSE)
			{
				list ($disposition, $filename) = preg_split('/; filename=/', $disposition);
			}

			$mime->addAttachment($attachment['attachment'], $content_type, $filename, FALSE, 'base64', $disposition, $charset, $language, $location, NULL, NULL, $description, Kohana::$charset);
		}

		// get must be called before headers
		$body = $mime->get();
		$headers = $mime->headers($this->headers);

		$this->error = $this->mail->send($this->to, $headers, $body);

		return $this->error === TRUE AND ! empty($body);
	}

}
