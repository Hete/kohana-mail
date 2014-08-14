<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * 
 * @package  Mail
 * @category Senders
 */
class Kohana_Mail_Sender_SwiftMailer_Smtp extends Mail_Sender_SwiftMailer {

	public function __construct(array $options)
	{
		parent::__construct($options);

		$this->transport = Swift_SmtpTransport::newInstance($this->options['host'], $this->options['port']);
	}

}
