<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PHPMailer sendmail sender.
 *
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @copyright (c) 2013, Hète.ca Inc.
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_PHPMailer_Sendmail extends Mail_Sender_PHPMailer {

	public function __construct(array $headers, array $options)
	{
		parent::__construct($headers, $options);

		$this->mailer->isSendmail();
	}

}
