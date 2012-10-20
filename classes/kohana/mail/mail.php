<?php

/**
 * 
 */
class Kohana_Mail_Mail extends Model {
    /*
     * Supported headers
     */

    const TO = "To",
            FROM = "From",
            BCC = "Bcc",
            MIME_VERSION = "MIME-Version",
            CONTENT_TYPE = "Content-type";

    public $content,
            $headers = array(
                'MIME-Version:' => 1.0,
                'Content-type' => 'text/html; charset=UTF-8',
                    ),
            $email,
            $subject;
    public $template;

    public function __construct($email, $subject, array $headers = array()) {
        $this->email = $email;
        $this->subject = $subject;



        $this->headers += $headers;
    }

    /**
     * Headers access method. Same as param.
     * @param string $key
     * @param string $value
     * @return type
     */
    public function headers($key = NULL, $value = NULL) {
        if ($key === NULL) {
            return $this->_headers;
        } else if ($value === NULL) {
            return $this->_headers[$key];
        } else {
            $this->_headers[$key] = $value;
            return $this;
        }
    }

    /**
     * 
     * @param type $name
     * @param type $arguments
     * @param type $render_callback
     * @return type
     */
    public function __call($name, $arguments = NULL) {

        if ($arguments === NULL) {
            return $this->headers(constant("Mail_Mail::HEADERS_" . strtoupper($name)));
        }


        if (count($arguments) !== 1) {
            throw new Kohana_Exception(":number arguments not supported.", array(":number" => count($arguments)));
        }

        // We set a key based on the constants.
        $key = constant("Mail_Mail::" . strtoupper($name));

        if ($key === NULL) {
            throw new Kohana_Exception("Header :header not supported.", array(":header" => $name));
        }


        $this->headers($key, $arguments[0]);
    }

    /**
     * Render the content of the mail.
     * @return string
     */
    public function render() {
        return $this->template->render();
    }

    /**
     * Alias for render().
     * @return type3
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Send the mail to its subject.
     * @return type
     */
    public function send() {

        return mail($this->email, '=?UTF-8?B?' . base64_encode($this->subject) . '?=', $this->render(), implode("\r\n", $this->headers));
    }

}

?>
