<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * PHPMailer-based mail sender.
 *
 * @package   Mail
 * @category  Senders
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2014, Guillaume Poirier-Morency
 * @license   BSD-3-Clauses
 */
abstract class Kohana_Mail_Sender_PHPMailer extends Mail_Sender {

	/**
	 *
	 * @var PHPMailer
	 */
	public $mailer;

	public function __construct(array $headers, array $options)
	{
		parent::__construct($headers, $options);

		$this->mailer = new PHPMailer;

		foreach ($this->options as $key => $value)
		{
			$this->mailer->{$key} = $value;
		}
	}

	public function error()
	{
		return $this->mailer->ErrorInfo;
	}

	protected function _send()
	{
		foreach ($this->headers as $name => $header)
		{
			if (Arr::is_array($header))
			{
				$header = $this->mailer->addrFormat($header);
			}

			$this->mailer->addCustomHeader($name, $header);
		}

		foreach ($this->to as $email => $name)
		{
			$this->mailer->addAddress(Valid::email($name) ? $name : $email, $name);
		}

		$this->mailer->Subject = $this->subject();

		$this->mailer->Body = $this->body;
		$this->mailer->isHTML(strpos('text/html', $this->content_type()) === 0);		

		foreach ($this->attachments as $attachment)
		{
			$headers = $attachment['headers'];

			$content_type = Arr::get($headers, 'Content-Type', '');
			$disposition = Arr::get($headers, 'Content-Disposition', 'attachment');
			$filename = NULL;
			$description = Arr::get($headers, 'Content-Description');

			if (strpos($disposition, '; filename=') !== FALSE)
			{
				list ($disposition, $filename) = preg_split('/;\s*filename=/', $disposition);
			}

			$this->mailer->addStringAttachment($attachment['attachment'], $filename, 'base64', $content_type, $disposition);
		}	

		return $this->mailer->send();
	}

}
