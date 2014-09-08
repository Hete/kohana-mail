<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Fake sender for testing application without sending real mails.
 *
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency <guillaumepoiriermorency@gmail.com>
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_Mock extends Mail_Sender {

	/**
	 * Stack of sent mail.
	 * 
	 * Use array_pop in your tests to ensure specific mail have been sent.
	 * 
	 * @var array 
	 */
	public static $history;

	/**
	 * Expose to for testing purposes.
	 */
	public $to;

	/**
	 * Expose attachments for testing purposes.
	 * 
	 * @var array 
	 */
	public $attachments = array();

	public function error()
	{
		if (empty($this->to))
		{
			return 'Recipient cannot be empty.';	
		}

		if (empty($this->body))
		{
			return 'Body cannot be empty.';	
		}

		return NULL;
	}

	protected function _send()
	{
		// push the mail on the stack
		static::$history[] = $this;

		// log the mocked mail for debugging
		Kohana::$log->add(Log::DEBUG, "Mocked mail for :to\n\n:headers\n\n:body", array(
			':to'      => print_r($this->to, TRUE),
			':headers' => print_r($this->headers, TRUE),
			':body'    => $this->body
		));

		return ! empty($this->to) AND ! empty($this->body);
	}

}
