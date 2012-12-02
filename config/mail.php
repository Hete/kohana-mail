<?php

return array(
    'default' => array(
        'from' => array(
            'name' => NULL,
            'email' => NULL,
        ),
        'subject' => '',
        // Async 
        'async' => FALSE, // If async is enabled, emails are not sent immediately but appended to a queue.
        'queue_path' => APPPATH . 'mailqueue',
        'salt' => NULL,
    ),
);
?>