# Kohana Mail Sender

Mail sender for kohana. It is based on 3 structures : Mail_Sender, Mail_Receiver and Mail_Queue. This sender allow you to easily send View based mails.

### It is as easy as
<pre>
Mail_Sender::factory()->send($receiver, $subject, $view, $parameters, $headers);
</pre>


### Or in a more configurable way..
<pre>
$receiver = Model::factory('Mail_Receiver');
$receiver->name = 'Foo';
$receiver->email = 'foO@example.com';

$mail = Model::factory('Mail');

$mail->receiver($receiver)
    ->subject('Bar is a good friend')
    ->content('As I told you..')
    ->headers('Content-type', 'text')
    ->reply_to('bar@example.com');
    
Mail_Sender::factory('any sender of your choice defaulted to Sendmail')->send($mail);    
</pre>

### Senders

It is designed with drivers, so you may use sendmail and smtp from PEAR Mail or the native mail function.

<pre>
Mail_Sender::factory("Native");

Mail_Sender::factory("PEAR_Sendmail");

Mail_Sender::factory("PEAR_SMTP");

$force = TRUE; // Force mail sending, event if user is not subscribed (changes nothing here, unless we use Mail_Receiver interface).
$result = Mail_Sender::factory()->send('foo@bar.com', 'Hey Foo, here is your activation key!', 'mail/activation', array('key' => $key), array('Bcc' => 'admin@bar.com'), $force);

if($result) {
    // Congratulations!
} else {
    // Try again :P (and check your mail setup..)
}

</pre>

### Receivers

Mail_Receiver is an interface that you may implement on your user models. You have to imlement a function returning its name, email and subscribtion to the specified view.

This module also provides a Model_MaiL_Receiver model and the ability to send to users specified in an mixed array of user => email, email => user, email and Mail_Receiver.

<pre>
$foo = Model::factory('MaiL_Receiver');
$foo->email = 'foo@bar.com';
$foo->name = 'Foo Bar';

Mail_Sender::factory()->send(array(
    'foo@bar.com' => 'Foo Bar',
    'Foo Bar' => 'foo@bar.com',
    $foo,
    'foo@bar.com'
));
</pre>

### Queues

This sender allow queue usage. Instead of sending a mail to a transport agent, you may store it temporairly using the Queue driver. Queue inherit from Sender, so you may send a mail to a queue. You can also retreive mail for sending them later or by setting a cron task.

<pre>
// Send like if you would use a Sender
Mail_Queue::factory()->send($receiver, $subject, $view, $parameters, $headers, $force);

// Push prepared mail
Mail_Queue::factory()->push($mail);

// You can retreive the mail later and send it
$mail = Mail_Queue::factory("File")->pull();
Mail_Sender::factory()->send($mail);

// You may also peek from the queue (not removing)
Mail_Queue::factory()->peek();
</pre>


### Styler

This sender has integrated styler capabilities for rich mail. You may also use the Auto styler which one will apply a Text::auto_p and a Text::auto_link to your mail content.

You could also store your style in a view or use file_get_contents(). It is cached, so it will not be recomputed over and over.

<pre>
$styler = Mail_Styler::factory('HTML'); // Defaulted to HTML, you might omit setting your styler.

Mail_Sender::factory()
    ->styler($styler) // Get or set styler
    ->style('div{background:blue;}') // Add css rules by aliasing ->styler()->style()
    ->send(); // Send your goodies to the world
</pre>
