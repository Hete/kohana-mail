<?php

class Kohana_Model_Mail extends Kohana_Model_Validation {

    /**
     *
     * @var Model_User 
     */
    public $receiver;
    public $content, $headers,
            $subject;

    /**
     * 
     * @param Model_User $receiver
     * @param type $subject
     * @param View $content
     * @param array $headers
     */
    public function __construct(Model_User $receiver, $subject, View $content, array $headers = NULL) {
        if ($headers === NULL) {            
            $headers = array(
                "To" => $receiver->email,
                "Subject" => $subject,                
                "Date" => date(Date::$timestamp_format),
                "Content-type" => 'text/html; charset=UTF-8',
                "MIME-Version" => 1.0
            );
        }
        $this->receiver = $receiver;
        $this->subject = base64_encode($subject);
        $this->content = $content;

        $this->headers = $headers;
    }

    /**
     * 
     * @return type
     */
    private function generate_headers() {
        $output = array();
        foreach ($this->headers as $key => $value) {
            $output[] = "$key: $value";
        }
        return implode("\r\n", $output);
    }

    /**
     * 
     * @param type $async
     * @return type
     */
    public function send($async = FALSE) {
        if ($async) {
            return Mail_Sender::instance()->push($this);
        }

        return mail($this->receiver->email, '=?UTF-8?B?' . $this->subject . '?=', $this->content->render(), $this->generate_headers());
    }

}

?>
