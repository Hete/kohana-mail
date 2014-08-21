<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender based on PEAR Sendmail.
 * 
 * @uses Mail_sendmail
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD-3-Clauses
 */
class Kohana_Mail_Sender_PEAR_Sendmail extends Mail_Sender_PEAR {

	public function __construct(array $options)
	{
		parent::__construct($options);

		$this->mail->factory('sendmail', $this->options);
	}

}
