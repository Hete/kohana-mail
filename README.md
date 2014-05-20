kohana-mail
===========

Simple mailer for the Kohana framework.

Supports the built-in ```mail``` function and the PEAR Mail module so you can send 
mail through smtp and sendmail.

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
        'Content-Disposition' => 'attachment; filename=cat.png'
    )
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
