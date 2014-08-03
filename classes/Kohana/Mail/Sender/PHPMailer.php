<?php
require Kohana::find_file('vendor', 'PHPMailer/PHPMailerAutoload');

/**
 * PHPMailer-based mail sender.
 *
 * @package Mail
 * @author Guillaume Poirier-Morency
 * @license BSD-3-Clauses
 */
class Kohana_Mail_Sender_PHPMailer extends Mail_Sender {

	public function _send(array $to)
	{
		$mailer = new PHPMailer();
		
		foreach ($this->options as $key => $value)
		{
			$mailer->{$key} = $value;
		}
		
		foreach ($this->headers as $key => $value)
		{
			$mailer->addCustomHeader($key, $value);
		}
		
		$mailer->Subject = $this->headers('Subject');
		$mailer->Body = $this->body;
		
		foreach ($this->attachments as $attachment)
		{
			$headers = $attachment['headers'];
			
			$disposition = Arr::get($headers, 'Content-Disposition', 'attachment');
			$filename = NULL;
			
			if (strpos($disposition, ';filename=') !== FALSE)
			{
				list ($disposition, $filename) = preg_split('/;filename=/', $disposition);
			}
			
			$mailer->addStringAttachment($attachment['attachment'], $filename, Arr::get($headers, 'Content-Encoding'), Arr::get($headers, 'Content-Type'), $disposition);
		}
		
		$mailer->isHTML($this->headers('Content-Type') === 'text/html');
		
		return $mailer->send();
	}
}
