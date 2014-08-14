<?php

defined('SYSPATH') or die('No direct script access.');

require Kohana::find_file('vendor', 'PHPMailer/PHPMailerAutoload');

/**
 * PHPMailer-based mail sender.
 *
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2014, Guillaume Poirier-Morency
 * @license BSD-3-Clauses
 */
abstract class Kohana_Mail_Sender_PHPMailer extends Mail_Sender {

	/**
	 *
	 * @var PHPMailer
	 */
	public $mailer;

	public function __construct(array $options)
	{
		parent::__construct($options);

		$this->mailer = new PHPMailer();

		foreach ($this->options as $key => $value)
		{
			$this->mailer->{$key} = $value;
		}
	}

	protected function _send(array $to)
	{
		foreach ($this->headers as $key => $value)
		{
			$this->mailer->addCustomHeader($key, $value);
		}

		foreach ($to as $address)
		{
			$this->mailer->addAddress($address);
		}

		$this->mailer->Subject = $this->headers('Subject');

		$this->mailer->Body = $this->body;

		foreach ($this->attachments as $attachment)
		{
			$headers = $attachment['headers'];

			$disposition = Arr::get($headers, 'Content-Disposition', 'attachment');
			$filename = NULL;

			if (strpos($disposition, ';filename=') !== FALSE)
			{
				list ($disposition, $filename) = preg_split('/;filename=/', $disposition);
			}

			$this->mailer->addStringAttachment($attachment['attachment'], $filename, Arr::get($headers, 'Content-Encoding'), Arr::get($headers, 'Content-Type'), $disposition);
		}

		$this->mailer->isHTML($this->headers('Content-Type') === 'text/html');

		return $this->mailer->send();
	}

}
