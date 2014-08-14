<?php

defined('SYSPATH') or die('No direct script access.');

require Kohana::find_file('vendor', 'swiftmailer/lib/swift_required');

class Kohana_Mail_Sender_SwiftMailer extends Mail_Sender {

	/**
	 * @var Swift_Transport
	 */
	public $transport;

	public function __construct(array $options)
	{
		parent::__construct($options);

		foreach ($this->options as $option => $value)
		{
			$this->transport->set{ucfirst($option)}($value);
		}
	}

	public function _send(array $to)
	{
		$message = Swift_Message::newInstance($this->subject(), $this->body());

		foreach ($this->headers as $header => $value)
		{
			$message->getHeaders()->addTextHeader($header, $value);
		}

		$message->setTo($to);

		foreach ($this->attachments as $attachment)
		{
			$headers = $attachment['headers'];

			$attachment = Swift_Attachment::newInstance($attachment['attachment']);

			foreach ($headers as $header => $value)
			{
				$attachment->getHeaders()->addTextHeader($header, $value);
			}

			$message->attach($attachment);
		}

		return $this->transport->send($message);
	}

}
