<?php

class Kohana_Mail_Mail {

    public $content, $headers, $email, $subject;

    public function __construct($email, $subject, $content, $headers) {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
        $this->headers = $headers;
    }

    public function send() {

        return mail($this->email, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $this->content, $this->headers);
    }

}

?>
