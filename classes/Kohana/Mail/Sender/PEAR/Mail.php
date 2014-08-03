<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Sender based on PEAR mail built-in function.
 *
 * @uses Mail_mail
 *      
 * @package Mail
 * @category Senders
 * @author Guillaume Poirier-Morency
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_PEAR_Mail extends Mail_Sender_PEAR {

	protected function PEAR_send(array $to, array $headers, $body)
	{
		$mail = new Mail();
		
		return $mail->factory('mail', $this->options)->send($to, $headers, $body);
	}
}
