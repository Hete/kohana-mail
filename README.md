kohana-mail
===========

Simple mailer for the Kohana framework.

Supports the following senders
* `mail` built-in function
* [PEAR Mail](http://pear.php.net/package/Mail/)
* [PHPMailer](https://github.com/PHPMailer/PHPMailer)
* mock sender for testing

The SMTP sender for PEAR Mail module uses old PHP4 code that throws strict 
warnings. If imported, it will automatically disable `E_STRICT`.

It aims to unify mailing system under a single interface so that you can
deploy your app independently of available libraries on the server or in your
organization.

The SMTP sender for PEAR Mail module uses old PHP4 code that throws strict 
warnings. If imported, it will automatically disable `E_STRICT`. It is 
recommended to use it in `PRODUCTION` environment and test with an alternate
sender.

Basic usage
-----------
```php
Mailer::factory()
    ->headers('Content-Type', 'text/html')
    ->subject('Hey :username!')
    ->body(View::factory('some_template'))
    ->param(':username', 'John McGuire')
    ->send(array('John McGuire' => 'foo@example.com' ));
```

The `Mail_Sender::param` function is used to substitute the body and 
subject. If you use a `View` for your body, it is more convenient to pass 
variables using `View::factory`.

Headers are set through the `headers` function. If the value is an `array`,
the header will be parsed as a RFC recipient list.

```php
$mailer->headers('Bcc', array('johndoe@example.com' => 'John Doe'));
```

Alias are defined for `Cc`, `Bcc`, `Reply-To` and more for convenience.

```php
$mailer->bcc('johndoe@example.com');
```

Attachments
-----------
Attachment content can be appended on a mail using `Mail_Sender::attachment`. 
You may specify an array of headers specific to that attachment.

Mail with attachment(s) will be automatically converted to multipart format.

```php
Mailer::factory()
    ->subject('Got a new cat picture for you.')
    ->attachment(file_get_contents('cat.png'), array(
        'Content-Type' => 'image/png',
        'Content-Disposition' => 'attachment; filename=cat.png')
    ->send('foo@example.com');
```

Receivers
---------
Receivers must comply with the following format:

A simple email

```php
$receiver = "john@example.com"; # a simple email
$receivers = array("john@example.com", "james@example.com"); # a list of emails
$receivers = array("john@example.com" => "John Doe # an associative array
$receivers = array("john@example.com", "james@example.com" => "James Doe"); # a mixed array
```

It is pretty convenient with the ORM
```php
$receivers = ORM::factory('user')
    ->find_all()
    ->as_array('email', 'full_name');

Mailer::factory()
    ->reply_to('noreply@example.com')
    ->body('Hey guys!')
    ->send($receivers);
```

Sending heavy mail
------------------
You can send heavy mail using `register_shutdown_function`

```php
register_shutdown_function(array($mailer, 'send'), $users);
```

Mail will be sent after the user gets his response.

Generating Message-ID
---------------------
There is a message id implementation based on 
[Matt Curtin & Jamie Zawinski recommendations](http://www.jwz.org/doc/mid.html). 
It generates secure identifier to make threads and other fancy mailing stuff.

```php
Mailer::factory()
    ->in_reply_to(Mailer::message_id())
    ->body('Hey Foo, long time no see!')
    ->send('foo@example.com')
```

Testing mail
------------
The module provides a Mock sender to make efficient testing. Mails are pushed in
a stack `Mail_Sender_Mock::$history` so that you can retreive them and test 
their content.

```php
public function testMail() 
{
    // self-request to send a mail
    Request::factory('send')->execute();

    $mail = array_pop(Mail_Sender_Mock::$history);
    
    $this->assertEquals('text/html', $mail->headers('Content-Type'));
    $this->assertContains('foo@example.com', $mail->to);
    
    $this->assertTag(array('tag' => 'a', 'attributes' => array('href' => 'http://example.com')), $mail->body());
}
```
