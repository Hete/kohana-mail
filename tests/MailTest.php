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

	public function providerHeaders() 
	{
		return array(
			array('test', 'test'),
			array('test ¤', 'test =?UTF-8?B?wqQ=?='),
			array('test@test.com', 'test@test.com'),
			array(array('test@test.com'), 'test@test.com'),
			array(array('test@test.com' => 'test'), 'test <test@test.com>'),
			array(array('test@test.com' => 'test', 'test2@test.com' => 'test'), 'test <test@test.com>, test <test2@test.com>'),
			array('test', 'test')
		);	
	}
	
	/**
	 * @dataProvider providerHeaders
	 */
	public function testEncodeHeaderWithMbstring($header, $encoded) 
	{
		$this->assertEquals($encoded, Mail_Sender_Mail::header_encode($header));
	}

	public function testMessageIDGenerator()
	{
		$this->assertRegExp('/<[\d\w\+=]+\.[\d\w\+=]+@\w+(\.\w+)*>/', Mailer::message_id());
	}

	public function providerSender()
	{
		return array(
			array(Mail_Sender::factory('Mail', array())),
			array(Mail_Sender::factory('PEAR_Mail', array())),
			array(Mail_Sender::factory('PEAR_Sendmail', array())),
			// array(Mail_Sender::factory('PEAR_SMTP', array())),
			array(Mail_Sender::factory('PHPMailer_Mail', array())),
			// array(Mail_Sender::factory('PHPMailer_Qmail', array())), // could not test this one :(
			array(Mail_Sender::factory('PHPMailer_Sendmail', array())),
			// array(Mail_Sender::factory('PHPMailer_SMTP', array())),
			array(Mail_Sender::factory('Mock', array()))
		);
	}

	public function testSendWithDefaultSender()
	{
		$sender = Mailer::factory();

		$this->assertTrue($sender->subject('test')
						->from(get_class(Mailer::factory()))
						->body('test')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSend(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('test')
						->from((get_class($sender)))
						->body('test')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSubject(Mail_Sender $sender)
	{
		// ascii subect
		$this->assertTrue($sender->subject('Hello Foo')
						->from(get_class($sender))
						->body('test')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSubjectNonAscii(Mail_Sender $sender)
	{
		// non-ascii subject
		$this->assertTrue($sender->subject('¤ Hello Foo ¤')
						->from(get_class($sender))
						->body('test')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSubjectEmpty(Mail_Sender $sender)
	{
		$this->assertTrue($sender
						->subject('')
						->from(get_class($sender))
						->body('test')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testSubjectOmmited(Mail_Sender $sender)
	{
		$this->assertTrue($sender
						->from(get_class($sender))
						->body('test')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testHeaders(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('test')
						->from(get_class($sender))
						->body('test')
						->headers('Content-Type', 'text/html')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testParam(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('Mail sent by :name')
						->from(get_class($sender))
						->body('Hi, it\'s :name, how are you?')
						->param(':name', 'Foo')
						->send(MailTest::RECEIVER), $sender->error());

		$this->assertEquals('Mail sent by Foo', $sender->subject());
		$this->assertEquals('Hi, it\'s Foo, how are you?', $sender->body());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testBodyHTML(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('Hey foo!')
						->from(get_class($sender))
						->content_type('text/html')
						->body('<html><body>Hey foo!</body></html>')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testBodyPlainText(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('Hey foo!')
						->from(get_class($sender))
						->body('Hey!')
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testBodyOmmited(Mail_Sender $sender)
	{
		$this->assertFalse($sender->subject('Hey foo!')
						->from(get_class($sender))
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testAttachment(Mail_Sender $sender)
	{
		$this->assertTrue($sender->subject('Sent you some files!')
						->from(get_class($sender))
						->body('Hey!')
						->attachment('{}', array('Content-Type' => 'application/json'))
						->attachment(file_get_contents(MODPATH.'mail/tests/test.png'), array(
							'Content-Type' => 'image/png'))
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testAttachmentWithFilename(Mail_Sender $sender)
	{
		// with filename
		$this->assertTrue($sender->subject('Sent you some files!')
						->from(get_class($sender))
						->body('Hey!')
						->attachment('{}', array('Content-Type' => 'application/json'))
						->attachment(file_get_contents(MODPATH.'mail/tests/test.png'), array(
							'Content-Type' => 'image/png',
							'Content-Disposition' => 'attachment;filename=test'))
						->send(MailTest::RECEIVER), $sender->error());
	}

	/**
	 * @dataProvider providerSender
	 */
	public function testAttachmentWithManyHeaders(Mail_Sender $sender)
	{
		// with filename
		$this->assertTrue($sender->subject('Sent you some files!')
						->from(get_class($sender))
						->body('Hey!')
						->attachment('test', array(
							'Content-Type' => 'text/plain; charset=utf-8',
							'Content-Disposition' => 'attachment; filename=test',
							'Content-Description' => 'lol',
							'Content-Language' => 'en'))
						->send(MailTest::RECEIVER), $sender->error());
	}

}
