<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Disable E_STRICT error reporting.
 */
error_reporting(error_reporting() & ~ E_STRICT);

/**
 * Sender based on PEAR SMTP.
 *
 * @uses Mail_smtp
 *      
 * @package  Mail
 * @category Senders
 * @author   Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license  BSD-3-Clauses
 */
class Kohana_Mail_Sender_PEAR_SMTP extends Mail_Sender_PEAR {

	public function __construct(array $headers, array $options)
	{
		parent::__construct($headers, $options);

		$this->mail->factory('smtp', $this->options);
	}

}
