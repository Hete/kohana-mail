<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * PHPMailer SMTP sender.
 * 
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_PHPMailer_SMTP extends Mail_Sender_PHPMailer {

	public function __construct(array $options)
	{
                parent::__construct($options);

		$this->mailer->isSMTP();
	}

}
