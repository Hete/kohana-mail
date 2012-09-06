<?php

return array(
    'default' => array(
        'from' => '', // Send mails from this address.
        'async' => FALSE, // If async is enabled, emails are not sent immediately but appended to a queue.
        'queue_path' => 'mailqueue',
    ),
);
?>