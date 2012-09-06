<?php

class Kohana_Mail_Mail {

    public $content, $headers, $email, $subject;

    public function __construct(string $email, string $subject, string $content, string $headers) {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
        $this->headers = $headers;
    }

}


?>
