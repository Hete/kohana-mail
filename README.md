kohana-mail
===========

Mail sender for kohana. It is based on 3 structures : Mail_Sender, Mail_Receiver and Mail_Queue. This sender allow you to easily send View based mails.

### It is as easy as
<pre>
Mail_Sender::factory()->send($receiver, $view, $subject, $headers);
</pre>

### Sender drivers

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

Mail_Queue::factory("File");
