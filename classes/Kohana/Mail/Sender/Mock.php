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
	 * Expose attachments for testing purposes.
	 *
	 * @var array
	 */
	public $attachments;

	protected function _send(array $to)
	{
		$this->to = $to;

		// push the mail on the stack
		Mail_Sender_Mock::$history[] = $this;

		// log the mocked mail for debugging
		Kohana::$log->add(Log::DEBUG, "Mocked mail for :to\n\n:headers\n\n:body", array(
			':to' => print_r($to, TRUE),
			':headers' => print_r($this->headers, TRUE), ':body' => $this->body));

		return (bool) $to;
	}

}
