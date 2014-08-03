<?php
defined('SYSPATH') or die('No direct script access.');

class Kohana_Mail_Sender_PHPMailer_Qmail extends Mail_Sender_PHPMailer {

	public function _send(array $to)
	{
		$this->mailer->isQmail();
	
		return parent::_send($to);
	}
}