<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for the Mail package.
 *
 * @package   Mail
 * @category  Tests
 * @author    Hète.ca Team
 * @copyright (c) 2013, Hète.ca Inc.
 * @license   BSD-3-Clauses
 */
class MailTest extends Unittest_TestCase {

	/**
	 * Set a custom email to receive the test results.
	 */
	const RECEIVER = 'guillaumepoiriermorency@gmail.com';

	public function setUp()
	{
		parent::setUp();

		if (MailTest::RECEIVER === NULL)
		{
			$this->markTestSkipped('You have to set a receiver in order to get the test results.');
		}
	}

	public function providerSender()
	{
		return array(
			array('Mail', array()),
			array('PEAR_Mail', array()),
			array('PEAR_Sendmail', array()),
			array('PEAR_SMTP', array()),
			array('PHPMailer_Mail', array()),
			array('PHPMailer_Qmail', array()),
			array('PHPMailer_Sendmail', array()),
			array('PHPMailer_SMTP', array()),
			array('Mock', array()));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSend($name, array $options)
	{
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('test')
						->from(($name))
						->body('test')
						->send(MailTest::RECEIVER));
	}

	public function testSendWithDefaultSender()
	{
		$this->assertTrue(Mailer::factory()->subject('test')
						->from(get_class(Mailer::factory()))
						->body('test')
						->send(MailTest::RECEIVER));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSubject($name, array $options)
	{
		// ascii subect
		$this->assertTrue(Mail_Sender::factory($name, $options)
						->subject('Hello Foo')
						->from($name)
						->body('test')
						->send(MailTest::RECEIVER));

		// non-ascii subject
		$this->assertTrue(Mail_Sender::factory($name, $options)
						->subject('¤ Hello Foo ¤')
						->from($name)
						->body('test')
						->send(MailTest::RECEIVER));

		// empty subject
		$this->assertTrue(Mail_Sender::factory($name, $options)
						->subject('')
						->from($name)
						->body('test')
						->send(MailTest::RECEIVER));

		// no subject at all
		$this->assertTrue(Mail_Sender::factory($name, $options)
						->from($name)
						->body('test')
						->send(MailTest::RECEIVER));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testHeaders($name, array $options)
	{
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('test')
						->from($name)
						->body('test')
						->headers('Content-Type', 'text/html')
						->send(MailTest::RECEIVER));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testParam($name, array $options)
	{
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('Mail sent by :name')
						->from($name)
						->body('Hi, it\'s :name, how are you?')
						->param(':name', 'Foo')
						->send(MailTest::RECEIVER));

		$this->markTestIncomplete();
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testBody($name, array $options)
	{
		// html body
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('Hey foo!')
						->from($name)
						->content_type('text/html')
						->body('<html><body>Hey foo!</body></html>')
						->send(MailTest::RECEIVER));

		// plain text body
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('Hey foo!')
						->from($name)
						->body('Hey!')
						->send(MailTest::RECEIVER));

		// ommited body
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('Hey foo!')
						->from($name)
						->send(MailTest::RECEIVER));
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testAttachment($name, array $options)
	{
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('Sent you some files!')
						->from($name)
						->body('Hey!')
						->attachment('{}', array('Content-Type' => 'application/json'))
						->attachment(file_get_contents(MODPATH.'mail/tests/test.png'), array(
							'Content-Type' => 'image/png'))
						->send(MailTest::RECEIVER));

		// with filename
		$this->assertTrue(Mail_Sender::factory($name, $options)->subject('Sent you some files!')
						->from($name)
						->body('Hey!')
						->attachment('{}', array('Content-Type' => 'application/json'))
						->attachment(file_get_contents(MODPATH.'mail/tests/test.png'), array(
							'Content-Type' => 'image/png',
							'Content-Disposition' => 'attachment;filename=test'))
						->send(MailTest::RECEIVER));
	}

	public function testMessageIDGenerator()
	{
		$this->assertRegExp('/<[\d\w\+=]+\.[\d\w\+=]+@\w+(\.\w+)*>/', Mailer::message_id());
	}

}
