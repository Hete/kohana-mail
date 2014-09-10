<?php

defined('SYSPATH') OR die('No direct script access.');

/**
 * Sender based on PEAR mail built-in function.
 *
 * @uses Mail_mail
 *      
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD-3-Clauses
 */
class Kohana_Mail_Sender_PEAR_Mail extends Mail_Sender_PEAR {

	public function __construct(array $headers, array $options)
	{
		parent::__construct($headers, $options);

		$this->mail->factory('mail', $this->options);
	}

}
