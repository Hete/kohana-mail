<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * 
 * 
 * @package   Mail
 * @category  Senders
 * @author    Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2014, Hète.ca Inc.
 */
class Kohana_Mail_Sender_SwiftMailer_Mail extends Mail_Sender_SwiftMailer {

	public function __construct(array $options)
	{
		parent::__construct($options);

		$this->transport = Swift_MailTransport::newInstance();
	}

}
