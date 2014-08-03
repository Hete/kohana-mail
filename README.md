kohana-mail
===========

Simple mailer for the Kohana framework.

Supports the following senders
* ```mail``` built-in function
* [PEAR Mail](http://pear.php.net/package/Mail/)
* [PHPMailer](https://github.com/PHPMailer/PHPMailer)
* mock sender for testing

The SMTP sender for PEAR Mail module uses old PHP4 code that throws strict warnings. If imported, it will automatically disable ```E_STRICT```.

## Basic usage

```php
Mailer::factory()
    ->headers('Content-Type', 'text/html')
    ->subject('Hey :username!')
    ->body(View::factory('some_template'))
    ->param(':username', 'John McGuire')
    ->send(array('John McGuire' => 'foo@example.com' ));
```

The ```Mail_Sender::param``` function is used to substitute the body and subject. If you use a 
View for your body, it is more convenient to pass variables using ```View::factory```.

## Attachments

Attachment content can be appended on a mail using ```Mail_Sender::attachment```. You may specify an array of 
headers specific to that attachment.

Mail with attachment(s) will be automatically converted to multipart format.

```php
Mailer::factory()
    ->subject('Got a new cat picture for you.')
    ->attachment(file_get_contents('cat.png'), array(
        'Content-Type' => 'image/png',
        'Content-Disposition' => 'attachment; filename=cat.png')
    ->send('foo@example.com');
```

## Receivers

Receivers could be 4 things

A simple email

```php
$receiver = "john@example.com";
```

A list of emails

```php
$receivers = array("john@example.com", "james@example.com");
```

An associative array

```php
$receivers = array("john@example.com" => "John Doe");
```

Or a mixed array

```php
$receivers = array("john@example.com", "james@example.com" => "James Doe");
```

It is pretty convinient with the ORM

```php
$receivers = ORM::factory('user')->find_all()->as_array('email', 'full_name');

Mailer::factory()
    ->reply_to('noreply@example.com')
    ->send($receivers);
```

## Sending heavy mail

You can send heavy mail using ```register_shutdown_function```

```php
register_shutdown_function(array($mailer, 'send'), $users);
```

It's pretty convenient to reduce the request time as mail can often take an
while to send.

## Generating Message-ID

There is a message id implementation based on [Matt Curtin & Jamie Zawinski recommendations](http://www.jwz.org/doc/mid.html). It generates
secure identifier to make threads and other fancy mailing stuff.

```php
$message_id = Mailer::message_id();

Mailer::factory()
    ->in_reply_to($message_id)
    ->body('Hey Foo, long time no see!')
    ->send('foo@example.com')
```

## Testing mail

The module provides a Mock sender to make efficient testing. Mails are pushed in a stack ```Mail_Sender_Mock::$history``` so that you can retreive them and test their content.

A variable ```$to``` is added in the mail sender to test receivers. It is an array of RFC822 compliant emails.

```php
public function testMail() 
{
    // self-request to send a mail
    Request::factory('send')->execute();

    $mail = array_pop(Mail_Sender_Mock::$history);
    
    $this->assertEquals('text/html', $mail->headers('Content-Type'));
    $this->assertContains('foo <foo@example.com>', $mail->to);
    
    $this->assertTag(array('tag' => 'a', 'attributes' => array('href' => 'http://example.com')), $mail->body());
}
```
