# Kohana Mail Sender
Simple mail sender for the Kohana framework.

## Basic usage

    Mail_Sender::factory()->send($receivers, $subject, $body, $headers);

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

### Subject
The subject have to be a string. It will be translated with a substitution for :email and :name.

    $subject = ':name, you have just subscribed with :email to my wonderful website!'
    
    $subject = 'mail.subject.subscription';

### Body
The body could be a string or a View, or anything that can be cased to string.

    $body = View::factory('mail.subscription');
  
    $body = "Hey John Doe! Thanks for subscribing!";

### Headers
The headers must be an array

    $headers = array('From' => 'noreply@example.com');
    'foo@bar.com'
));
</pre>

## HTML templating
You can easily do HTML templating and even styling! There is a CSS inliner included :)

To activate that feature,

    Mail_Styler::$default = 'Styler_HTML';

You should also set the Content-Type to text/html in the configuration. It comes with a light bootstrap css.

## Processing stuff
If you override Mail_Sender, you should really look for the subject, body and headers process function.

If you need to access the user who's getting your mail:

    protected function process_body($body, $email, $name = NULL) {

        $body->user = ORM::factory('user', array('email' => $email));

        return parent::process_body($body, $email, $name);
    }
