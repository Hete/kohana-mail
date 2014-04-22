kohana-mail
===========

Simple mailer for the Kohana framework.

Supports the built-in mail() function and the PEAR Mail module so you can send 
mail through smtp and sendmail.

## Basic usage

    Mailer::factory()
        ->headers('Content-Type', 'text/html')
        ->subject('Hey :username!')
        ->body(View::factory('some_template'))
        ->param(':username', 'John McGuire')
        ->send(array(
            'John McGuire' => 'foo@example.com'
        ));

## Attachments

Attachmment content can be appended on a mail. You may specify an array of 
headers specific to that attachment.

    Mailer::factory()
        ->subject('Got a new cat picture for you.')
        ->attachment(file_get_contents('cat.png'), array(
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename=cat.png'
        )
        ->send('foo@example.com');

## Receivers

Receivers could be 4 things

A simple email

    $receiver = "john@example.com";

A list of emails

    $receivers = array("john@example.com", "james@example.com");

An associative array

    $receivers = array("john@example.com" => "John Doe");

Or a mixed array

    $receivers = array("john@example.com", "james@example.com" => "James Doe");

It is pretty convinient with the ORM

    $receivers = ORM::factory('user')->find_all()->as_array('email', 'full_name');

    Mailer::factory()
        ->reply_to('noreply@example.com')
        ->send($receivers);

## Sending heavy mail

You can send heavy mail using register_shutdown_function

    register_shutdown_function($mailer, 'send', $users);

It's pretty convenient to reduce the request time as mail can often take an
eternity to send.