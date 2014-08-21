<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sender based on PEAR mail built-in function.
 * 
 * @uses Mail_mail
 * 
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency
 * @license  BSD-3-Clauses
 */
class Kohana_Mail_Sender_PEAR_Mail extends Mail_Sender_PEAR {

	public function __construct(array $options)
	{
		parent::__construct($options);

		$this->mail->factory('mail', $this->options);
	}

}
