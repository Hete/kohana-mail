# Kohana Mail Sender

Mail sender for kohana. It is based on 3 structures : Mail_Sender, Mail_Receiver and Mail_Queue. This sender allow you to easily send View based mails.

This version of the module is deprecated. You are better use the [https://github.com/Hete/kohana-mail/tree/3.3/master](3.3/master) branch which contain a clean, simple and efficient implementation.

### It is as easy as
<pre>
Mail_Sender::factory()->send($receiver, $view, $subject, $headers);
</pre>

### Senders

It is designed with drivers, so you may use sendmail and smtp from PEAR Mail or the native mail function.

<pre>
Mail_Sender::factory("Native");

Mail_Sender::factory("PEAR_Sendmail");

Mail_Sender::factory("PEAR_SMTP");
</pre>

### Receivers

Mail_Receiver is an interface that you may implement on your user models. It only requires you to have return a name and a email. Receivers may be Traversable to send a dynamic mail to many receivers.

### Queues

This sender allow queue usage. Instead of sending a mail to a transport agent, you may store it temporairly using the Queue driver. Queue inherit from Senders, so you may send a mail to a queue. You can also retreive mail for sending them later or by setting a cron task.

<pre>
Mail_Queue::factory("File")->send($receiver, $view, $subject, $headers);

// You can retreive the mail later and send it
$mail = Mail_Queue::factory("File")->pull();
Mail_Sender::factory()->send($mail);
</pre>
