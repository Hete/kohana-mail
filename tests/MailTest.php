<?php
defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 *
 * @package Mail
 * @category Tests
 * @author Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 * @license BSD-3-Clauses
 */
class MailTest extends Unittest_TestCase {

	/**
	 * Set a custom email to receive the test results.
	 */
	const RECEIVER = 'foo@example.com';

	public function providerSender()
	{
		return array(array(Mail_Sender::factory('Mail', array())), 
			array(Mail_Sender::factory('PEAR_Mail', array())), 
			array(Mail_Sender::factory('PEAR_Sendmail', array())), 
			array(Mail_Sender::factory('PEAR_SMTP', array())), 
			array(Mail_Sender::factory('PHPMailer_Mail', array())), 
			array(Mail_Sender::factory('PHPMailer_Qmail', array())), 
			array(Mail_Sender::factory('PHPMailer_Sendmail', array())), 
			array(Mail_Sender::factory('PHPMailer_SMTP', array())), 
			array(Mail_Sender::factory('Mock', array())));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSend(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('test')
			->body('test')
			->send('foo@example.com'));
	}

	public function testSendWithDefaultSender()
	{
		$this->assertTrue(Mailer::factory()->subject('test')
			->body('test')
			->send('foo@example.com'));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSubject(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('Hello Foo')
			->body('test')
			->send(MailTest::RECEIVER));
		
		$this->assertTrue($sender->subject('¤ Hello Foo ¤')
			->body('test')
			->send(MailTest::RECEIVER));
		
		$this->assertTrue($sender->subject('')
			->body('test')
			->send(MailTest::RECEIVER));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testHeaders(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('test')
			->body('test')
			->headers('Content-Type', 'text/html')
			->send(MailTest::RECEIVER));
	}

	public function testParam()
	{
		$this->assertTrue(Mailer::factory()->subject('Mail sent by :name')
			->body('Hi, it\'s :name, how are you?')
			->param(':name', 'Foo')
			->send(MailTest::RECEIVER));
		
		$this->markTestIncomplete();
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testBody(Mail_Sender $sender)
	{
		// html body
		$this->assertTrue($sender->subject('Hey foo!')
			->content_type('text/html')
			->body('<html><body>Hey foo!</body></html>')
			->send(MailTest::RECEIVER));
		
		// plain text body
		$this->assertTrue($sender->subject('Hey foo!')
			->body('Hey!')
			->send(MailTest::RECEIVER));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testAttachment(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('Sent you some files!')
			->body('Hey!')
			->attachment('{}', array('Content-Type' => 'application/json'))
			->attachment(file_get_contents(MODPATH . 'mail/tests/test.png'), array(
			'Content-Type' => 'image/png'))
			->send(MailTest::RECEIVER));
		
		// with filename
		$this->assertTrue($sender->subject('Sent you some files!')
			->body('Hey!')
			->attachment('{}', array('Content-Type' => 'application/json'))
			->attachment(file_get_contents(MODPATH . 'mail/tests/test.png'), array(
			'Content-Type' => 'image/png', 
			'Content-Disposition' => 'attachment;filename=test'))
			->send(MailTest::RECEIVER));
	}

	public function testMessageIDGenerator()
	{
		$this->assertRegExp('/<[\d\w\+=]+\.[\d\w\+=]+@\w+(\.\w+)*>/', Mailer::message_id());
	}
}
