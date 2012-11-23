<?php

class Kohana_Model_Mail_Mail extends Model {

    public $content, $headers, $email, $subject;

    public function __construct($email, $subject, $content, $headers) {
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
        $this->headers = $headers;
    }

    public function render() {

        return $this->content;
    }

    public function send() {

        return mail($this->email, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $this->content, $this->headers);
    }

}

?>
