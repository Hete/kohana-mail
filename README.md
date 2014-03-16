# Kohana Mail Sender

Simple mail sender for the Kohana framework.

## Basic usage

    Mailer::factory()
        ->subject('Hey Foo!')
        ->body(View::factory('some_template'), 'text/html')
        ->send(array(
            'John McGuire' => 'foo@example.com'
        ));

## Attachments

Attachmment content can be appended on a mail. You may specify an array of headers specific to that attachment.

    Mail_Sender::factory()
        ->subject('Got a new cat picture for you.')
        ->attachment(file_get_contents('cat.png'), array(
            'Content-Type' => 'image/png'
        )
        ->send('foo@example.com')

### Receivers

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