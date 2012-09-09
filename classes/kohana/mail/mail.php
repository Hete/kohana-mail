<?php

class Kohana_Mail_Mail {

    public $content, $headers, $email, $subject;

    public function __construct($email, $subject, $content, $headers) {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
        $this->headers = $headers;
    }

}


?>
