<?php

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
	}

	protected function _send()
	{
		if (array_key_exists('Host', $this->options))
		{
			$this->mailer->isSMTP();
		}

		foreach ($this->options as $key => $value)
		{
			$this->mailer->{$key} = $value;
		}

		foreach ($this->headers as $name => $header)
		{
			if (Arr::is_array($header))
			{
				$header = $this->mailer->addrFormat($header);
			}

			$this->mailer->addCustomHeader($name, $header);
		}

		foreach ($this->to as $address)
		{
			$this->mailer->addAddress($this->mailer->addrFormat($address));
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
