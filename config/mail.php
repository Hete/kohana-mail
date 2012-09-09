<?php

return array(
    'default' => array(
        'from' => '', // Send mails from this address.
        'from_name' => '',
        'async' => TRUE, // If async is enabled, emails are not sent immediately but appended to a queue.
        'queue_path' => APPPATH . '/mailqueue',
        'salt' => 'asdasfafj239r8290qut5gq',
    ),
);
?>